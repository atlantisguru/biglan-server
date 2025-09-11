<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\NetworkEdges;
use App\Models\NetworkPrinters;
use App\Models\NetworkPrinterEvents;
use App\Models\NetworkPrinterStatistics;
use App\Models\NetworkPrinterSupplies;
use Carbon\Carbon;
use App\Models\GlobalSettings;
use App\Models\Documents;
use Illuminate\Support\Facades\Auth;

class NetworkPrintersController extends Controller
{

	/*
	 * Processing the payload based on "action" (Network Printers)
	 */
	public function payload(Request $request) {
    
    	$action = $request["action"];
    
    	switch($action){
	  	 	case "queryNetworkPrinters":
				return $this->queryNetworkPrinters($request);
				break;
        	case "queryNetworkPrinterSupplies":
				return $this->queryNetworkPrinterSupplies($request);
				break;
        	case "updateNetworkPrinter":
				return $this->updateNetworkPrinter($request);
				break;
        	case "deleteNetworkPrinter":
				return $this->deleteNetworkPrinter($request);
				break;
        	case "viewNetworkPrinter":
				return $this->viewNetworkPrinter($request);
				break;
        
        	default:
    			return null;
    	}
    
    
    }

	/*
	 * Gives back all the data of a Network Printer (Network Printers)
	 */
	public function viewNetworkPrinter(Request $request) {
    
   	 	if(!auth()->user()->hasPermission('read-network-printers')) { return redirect('dashboard'); }
    
    	$printer = NetworkPrinters::where("id", $request["id"])->first();
    	
    	$events = NetworkPrinterEvents::where("printer_id", $request["id"])->orderBy("created_at", "DESC")->where('created_at', '>', now()->subDays(30)->endOfDay())->get();
    	
    	$statistics = NetworkPrinterStatistics::select(\DB::raw("black_toner_level, DATE(created_at) as datum, print_counter"))->where("printer_id", $request["id"])->orderBy("created_at", "DESC")->where('created_at', '>', now()->subDays(30)->endOfDay())->get();
    	$paperJam = $events->where("event", "Paper Jam")->count();
    	$maintenance = $events->where("event", "Maintenance Required")->count();
    	
    	//Toner meddig elég kb?
    	if ($statistics->count() != 0) {
    		$tonerMin = $statistics->first()->black_toner_level;
    		$tonerMax = $statistics->last()->black_toner_level;
        	$days = 0;
    		foreach($statistics as $stat) {
        		$days = $days + 1;
        		if ($stat->black_toner_level > $tonerMax) {
            		$tonerMax = $stat->black_toner_level;
            		$days = 0;
            	}
        	}
        	$tonerLoss = $tonerMax - $tonerMin;
        } else {
        	$tonerLoss = 0;
        }
    	
    	if ($tonerLoss > 0) {
        	$tonerLossPerDay = $tonerLoss / $days;
        } else {
        	$tonerLossPerDay = 0;
        }
    
    	if ($tonerLossPerDay > 0) {
    		$tonerLeftInDays = floor($printer->black_toner_level / $tonerLossPerDay);
        } else {
        	$tonerLeftInDays = 0;
        }
    	
    	$tonerRemaining = "";
    	if ($tonerLeftInDays > 0) {
        	$lastDate = $date = Carbon::now()->addDays($tonerLeftInDays)->format("Y.m.d");
    		$tonerRemaining = __('all.network_printers.toner_approx_enough', ['days' => $tonerLeftInDays, 'date' => $lastDate]);
        }
    	
    	if ($statistics->count() > 0) {
    		$printed = ($statistics->first()->print_counter) - ($statistics->last()->print_counter);
        } else {
        	$printed = 0;
        }
    
    	$tonerArray = $this->generateDateArray();
    	$i = 0;	
    	foreach($tonerArray as $item) {
        	$toner = $statistics->where("datum", $item["date"])->first();
        	$tonerMax = $printer->black_toner_max;
        	$tonerArray[$i]["black_toner"] = 0;
        	if (isset($toner)) {
        		if($tonerMax > 0 && $toner->black_toner_level >= 0) {
        			$tonerArray[$i]["black_toner"] = round($toner->black_toner_level/$tonerMax*100);
            	}
            }
        	$i++;
        }
    	
    	return response()->json([
        			"printer" => $printer,
        			"events" => $events,
        			"statistics" => $statistics,
        			"paperjam" => $paperJam,
        			"maintenance" => $maintenance,
        			"printed" => $printed,
        			"tonerarray" => $tonerArray,
        			"tonerRemaining" => $tonerRemaining
        ]);
    
    }

	/*
	 * Updates the value of a field of a Network Printer (Network Printers)
	 */
	public function updateNetworkPrinter($request) {
    
    	$id = $request["id"];
    	$field = $request["field"];
    	$value = $request["value"];
    	$networkprinter = NetworkPrinters::where("id", $id)->first();
    	$networkprinter->$field = $value;
    	
    	$save = $networkprinter->save();
    
    	if ($save) {
        	return "OK";
        }
    
    }

	/*
	 * Gives back 30 day array for printer statistics (Network Printers)
	 */
	public function generateDateArray()	{
    	$dateArray = [];

    	$currentTime = Carbon::now();
		$hour = $currentTime->hour;

		if ($hour < 10) {
			$endDate = Carbon::today()->subDays(1);
	        $startDate = Carbon::today()->subDays(31);
        } else {
			$endDate = Carbon::today();
        	$startDate = Carbon::today()->subDays(30);
        }
    	
    	while ($startDate->lte($endDate)) {
        	$dateArray[]["date"] = $startDate->format('Y-m-d');
        	$startDate->addDay();
    	}

    	return $dateArray;
	}


    /*
	 * Gives back the list view of Network Printers (Network Printers)
	 */
	public function listNetworkPrinters() {
    
    	if(!auth()->user()->hasPermission('read-network-printers')) { return redirect('dashboard'); }
    	
		$networkPrinters = NetworkPrinters::orderBy("alias", "ASC")->get();
    
    	return view("networkprinters.list", compact('networkPrinters'));
    	
    }

	/*
	 * Form to create a new Network Printer (Network Printers)
	 */
	public function newNetworkPrinter() {
    
    	if(!auth()->user()->hasPermission('write-network-printer')) { return redirect('dashboard'); }
    
    	return view("networkprinters.new");
    
    }

	/*
	 * Saves the new Network Printer and gets additional informations from it via snmp (Network Printers)
	 */
	public function createNetworkPrinter(Request $request) {
   
   	 	if(!auth()->user()->hasPermission('write-network-printer')) { return redirect('dashboard'); }
   
    
    	$networkPrinter = new NetworkPrinters();
    	
    	$networkPrinter->alias = $request["alias"];
    	$networkPrinter->ip = $request["ip"];
    	$networkPrinter->save();
    
    	$this->snmpQueryPrinter($networkPrinter);
    
    	return redirect("/networkprinters");
    
    }


	/*
	 * Removes a Network Printer from the database with all the related informations and creates an HTML file to the Documents (Network Printers)
	 */
	public function deleteNetworkPrinter(Request $request) {
    
   	 	if(!auth()->user()->hasPermission('delete-network-printer')) { return redirect('dashboard'); }
   
    	$networkPrinter = NetworkPrinters::where("id", $request["id"])->first();
    
    	$networkPrinterEvents = NetworkPrinterEvents::where("printer_id", $request["id"])->get();
    	$networkPrinterStatistics = NetworkPrinterStatistics::where("printer_id", $request["id"])->get();
    
    	$content = view('networkprinters.archive', compact('networkPrinter', 'networkPrinterEvents', 'networkPrinterStatistics'))->render();
    	$filename = "arhivalt-halozati-nyomtato-".$networkPrinter->alias."-".$networkPrinter->id.".html";
    	//archív html létrehozása
    	file_put_contents(storage_path("documents/".$filename), $content);
    
    	//archív html fájl rögzítése dokumentumtárban
    	$doc = new Documents();
    	$doc->title = "Archivált hálózati nyomtató - " . $networkPrinter->alias . " - " . $networkPrinter->serial . " - " . $networkPrinter->invenory_id;
    	$doc->keywords = "archív,nyomtató,".$networkPrinter->alias.",".$networkPrinter->serial.",".$networkPrinter->brand.",".$networkPrinter->inventory_id.",".$networkPrinter->mac;
    	$doc->source = "generated";
    	$doc->filename = $filename;
    	$doc->filesize = filesize(storage_path("documents/".$filename));
    	$doc->signed_at = Carbon::now()->format("Y-m-d");
    	$doc->user_id = Auth::user()->id;
    	$doc->save();
    	
    	NetworkEdges::where("source", "pr".$request["id"])->delete();
    	NetworkEdges::where("target", "pr".$request["id"])->delete();
    	NetworkPrinterStatistics::where("printer_id", $request["id"])->delete();
    	NetworkPrinterEvents::where("printer_id", $request["id"])->delete();
    	NetworkPrinters::where("id", $request["id"])->delete();
    
    	return redirect("/networkprinters");
    
    }

	/*
	 * It should store events from SNMP Trapper (Network Printer/Events)
	 * TODO: Make it function. It needs a configured snmp trapper under Ubuntu.
	 * Also needs a bash script to process events and send to this function to store.
	 */
	public function SNMPEvent(Request $request) {
    
    	$printer = NetworkPrinters::where("ip", $request["ip"])->first();
    	$event = $request["event"];
    
    	if ($printer != null) {
    		$printerEvent = new NetworkPrinterEvents();
        	$printerEvent->printer_id = $printer->id;
        	$printerEvent->event = $event;
        	$printerEvent->save();	
        }
        
    }

	/*
	 * Starts the snmpQueryPrinter function for all printers (Network Printers)
	 */
	public function queryNetworkPrinters() {
    
    	$queryTime = Carbon::now()->format("Y-m-d H:i:s");
    	$printers = NetworkPrinters::get();
    	
    	
    	foreach($printers as $printer) {
    	
        	$this->snmpQueryPrinter($printer);
        
        }
    
    	$updatedPrinters = NetworkPrinters::where("updated_at", ">=", $queryTime)->get();
    
    	return $updatedPrinters;
    
    }

	/*
	 *  TODO: Get the printer supplies informations
	 */
	public function snmpQueryPrinterSupplies($printer) {
    	
    		$snmpReadCommunity = GlobalSettings::where("name", "snmp-read-community")->first()->value;
    
    		if(!isset($snmpReadCommunity)) {
            	$snmpReadCommunity = "public";
            }
    
    		$prtMarkerSuppliesType = ".1.3.6.1.2.1.43.11.1.1.5.1";
    		$prtMarkerSuppliesDescription = ".1.3.6.1.2.1.43.11.1.1.6.1";
    		$prtMarkerSuppliesMaxCapacity = ".1.3.6.1.2.1.43.11.1.1.8.1";
    		$prtMarkerSuppliesLevel = ".1.3.6.1.2.1.43.11.1.1.9.1";
    		
    		snmp_set_valueretrieval(SNMP_VALUE_LIBRARY);
            
    		//$values = @snmpwalk($printer->ip, $snmpReadCommunity, $prtMarkerSuppliesType, 50000);
			/*
    		if (is_array($values)) {
				foreach ($values as $value) {
                	//\Log::info("type: " . explode(": ", $value)[1]);
				}
            }
    		*/
    		
    		$values = @snmpwalk($printer->ip, $snmpReadCommunity, $prtMarkerSuppliesDescription, 50000);

			if (is_array($values)) {
				foreach ($values as $value) {
                	$value = explode(": ", $value)[1];
                	$value = str_replace(array("\n", "\r"), '', $value);
                	if (preg_match('/^[0-9a-fA-F]+$/', str_replace(" ", "", $value)) === 1) {
                    	$value = hex2bin(str_replace(" ", "", $value));
                    }
				}
            }
			
    		/*
    		$values = @snmpwalk($printer->ip, $snmpReadCommunity, $prtMarkerSuppliesMaxCapacity, 50000);

			if (is_array($values)) {
				foreach ($values as $value) {
    				//\Log::info("max: " . explode(": ", $value)[1]);
				}
            }
        
    		$values = @snmpwalk($printer->ip, $snmpReadCommunity, $prtMarkerSuppliesLevel, 50000);

			if (is_array($values)) {
				foreach ($values as $value) {
    				//\Log::info("level: " . explode(": ", $value)[1]);
				}
            }
            */
    
    }

	/*
	 *  Gets the printer data via SNMP
	 */
	public function snmpQueryPrinter($printer) {
    
    		$snmpReadCommunity = GlobalSettings::where("name", "snmp-read-community")->first()->value;
    
    		if(!isset($snmpReadCommunity)) {
            	$snmpReadCommunity = "public";
            }
    
    		if (!isset($printer->mac)) {
            	snmp_set_valueretrieval(SNMP_VALUE_LIBRARY);
                
                $mac = @snmpget($printer->ip, $snmpReadCommunity, ".1.3.6.1.2.1.2.2.1.6.1", 50000);
    
    			if ($mac !== false && trim($mac) != "" && trim($mac) != "\"\"") {
                	$printer->mac = $this->padMacAddress(explode(": ", $mac)[1]);
            	} else {
            		$mac = @snmpget($printer->ip, $snmpReadCommunity, ".1.3.6.1.2.1.2.2.1.6.2", 50000);
    				if ($mac !== false && trim($mac) != "") {
            			$printer->mac = $this->padMacAddress(explode(": ", $mac)[1]);
            		}
           		}
            }
            
    		snmp_set_valueretrieval(SNMP_VALUE_PLAIN);
            
        	
        	if (!isset($printer->brand)) { 
            	$brand = @snmpget($printer->ip, $snmpReadCommunity, ".1.3.6.1.2.1.25.3.2.1.3.1", 50000);
            	if ($brand !== false) { 
                	$printer->brand = $brand;
                }
        	}
        	
        	if (!isset($printer->serial)) {
     			$serialOID = ".1.3.6.1.2.1.43.5.1.1.17.1";
                
            	if (strpos($printer->brand, "Canon MF") !== false) {
                	$serialOID = ".1.3.6.1.4.1.1602.1.2.1.4.0";
                }
	
            	$serial = @snmpget($printer->ip, $snmpReadCommunity, $serialOID , 50000);	

            	if ($serial !== false) { 
                	$printer->serial = $serial;
                }
        	}
        	
        	$is_color_capable = @snmpget($printer->ip, $snmpReadCommunity, ".1.3.6.1.2.1.43.10.2.1.6.1.1", 50000);
        	if ($is_color_capable !== false) {
            	if($is_color_capable > 1) {
               		$printer->is_color_capable = 1;
                
                	$first_color = @snmpget($printer->ip, $snmpReadCommunity, ".1.3.6.1.2.1.43.11.1.1.6.1.1", 50000);
                	if (strpos(strtolower($first_color), "black") !== false) {
                    
                    	//KCMY sorrend, például EPSON ink nyomtatók
                    
                    	$black_color_max_oid = ".1.3.6.1.2.1.43.11.1.1.8.1.1";
                		$black_color_level_oid = ".1.3.6.1.2.1.43.11.1.1.9.1.1";
                    	
                    	$cyan_color_max_oid = ".1.3.6.1.2.1.43.11.1.1.8.1.2";
                		$cyan_color_level_oid = ".1.3.6.1.2.1.43.11.1.1.9.1.2";
                    
                    	$magenta_color_max_oid = ".1.3.6.1.2.1.43.11.1.1.8.1.3";
                		$magenta_color_level_oid = ".1.3.6.1.2.1.43.11.1.1.9.1.3";
                    	
                    	$yellow_color_max_oid = ".1.3.6.1.2.1.43.11.1.1.8.1.4";
                		$yellow_color_level_oid = ".1.3.6.1.2.1.43.11.1.1.9.1.4";
                    
                    } else {
                    
                    	//CMYK sorrend
                    
                    	$cyan_color_max_oid = ".1.3.6.1.2.1.43.11.1.1.8.1.1";
                		$cyan_color_level_oid = ".1.3.6.1.2.1.43.11.1.1.9.1.1";
                    
                    	$magenta_color_max_oid = ".1.3.6.1.2.1.43.11.1.1.8.1.2";
                		$magenta_color_level_oid = ".1.3.6.1.2.1.43.11.1.1.9.1.2";
                    	
                    	$yellow_color_max_oid = ".1.3.6.1.2.1.43.11.1.1.8.1.3";
                		$yellow_color_level_oid = ".1.3.6.1.2.1.43.11.1.1.9.1.3";
                    
                    	$black_color_max_oid = ".1.3.6.1.2.1.43.11.1.1.8.1.4";
                		$black_color_level_oid = ".1.3.6.1.2.1.43.11.1.1.9.1.4";
                    	
                    }
                
            		$cyan_toner_max = @snmpget($printer->ip, $snmpReadCommunity, $cyan_color_max_oid, 50000);
        			if ($cyan_toner_max !== false) { 
               			$printer->cyan_toner_max = $cyan_toner_max;
            		}
                
                	$cyan_toner_level = @snmpget($printer->ip, $snmpReadCommunity, $cyan_color_level_oid, 50000);
        			if ($cyan_toner_level !== false) { 
               			$printer->cyan_toner_level = $cyan_toner_level;
            		}
                
                	$magenta_toner_max = @snmpget($printer->ip, $snmpReadCommunity, $magenta_color_max_oid, 50000);
        			if ($magenta_toner_max !== false) { 
               			$printer->magenta_toner_max = $magenta_toner_max;
            		}
                
                	$magenta_toner_level = @snmpget($printer->ip, $snmpReadCommunity, $magenta_color_level_oid, 50000);
        			if ($magenta_toner_level !== false) { 
               			$printer->magenta_toner_level = $magenta_toner_level;
            		}
                	
                	$yellow_toner_max = @snmpget($printer->ip, $snmpReadCommunity, $yellow_color_max_oid, 50000);
        			if ($yellow_toner_max !== false) { 
               			$printer->yellow_toner_max = $yellow_toner_max;
            		}
                
                	$yellow_toner_level = @snmpget($printer->ip, $snmpReadCommunity, $yellow_color_level_oid, 50000);
        			if ($yellow_toner_level !== false) { 
               			$printer->yellow_toner_level = $yellow_toner_level;
            		}
                	
                } else {
                	$black_color_max_oid = ".1.3.6.1.2.1.43.11.1.1.8.1.1";
                	$black_color_level_oid = ".1.3.6.1.2.1.43.11.1.1.9.1.1";
                }
            } else {
            	$black_color_max_oid = ".1.3.6.1.2.1.43.11.1.1.8.1.1";
                $black_color_level_oid = ".1.3.6.1.2.1.43.11.1.1.9.1.1";
            }
        	
    		$black_toner_max = @snmpget($printer->ip, $snmpReadCommunity, $black_color_max_oid, 50000);
        	if ($black_toner_max !== false) { 
               	$printer->black_toner_max = $black_toner_max;
            }
        	
        	$black_toner_level = @snmpget($printer->ip, $snmpReadCommunity, $black_color_level_oid,50000);
        	if ($black_toner_level !== false) { 
               	$printer->black_toner_level = $black_toner_level;
            }
        	
    		if (strpos($printer->brand, "Canon iR-ADV") === false) {
        		$print_counter = @snmpget($printer->ip, $snmpReadCommunity, ".1.3.6.1.2.1.43.10.2.1.4.1.1", 50000);
        		if ($print_counter !== false) { 
               		$printer->print_counter = $print_counter;
            	}
            } else {
            	$print_counter = @snmpget($printer->ip, $snmpReadCommunity, ".1.3.6.1.4.1.1602.1.11.1.3.1.4.102", 50000);
        		if ($print_counter !== false) { 
               		$printer->print_counter = $print_counter;
            	}
            }
            
            
        	$printer->save();
    }

	/*
	 *  Helper function. If the MAC starts with 0s, the printer will not return these during the SNMP query, they must be completed from the left.
	 */
	public function padMacAddress($macAddress) {
  	
    	$macParts = explode(':', $macAddress);
    	
    	foreach ($macParts as &$part) {
    
        	$part = str_pad($part, 2, '0', STR_PAD_LEFT);
    	
        }
    	
    	return strtoupper(implode(':', $macParts));
	
    }

}
