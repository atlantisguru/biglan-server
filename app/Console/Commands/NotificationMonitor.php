<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Notifications;
use App\Models\NotificationLogs;
use Illuminate\Support\Facades\DB;
use App\Models\Workstations;
use Carbon\Carbon;
use App\Http\Controllers\WorkstationsController;
use App\Models\GlobalSettings;

class NotificationMonitor extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'notificationmonitor:check';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check monitored services status.';

	public $messages = array();

   	/**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
    
    	
    	$notifications = Notifications::where("monitored", "=", "1")->get();
    
    	foreach($notifications as $notification) {
        	
        	switch($notification->type) {
            	case "socket-polling":
            		$this->socketPolling($notification);
            		break;
            	case "ping":
            		$this->ping($notification);
            		break;
            	case "mass-heartbeat-loss":
            		$this->massHeartbeatLoss($notification);
            		break;
            	case "sensor-value":
            		$this->checkSensorValue($notification);
            		break;
            	case "biglan-command":
            		$this->biglanCommand($notification);
            		break;
            	case "snmp":
            		$this->snmp($notification);
            		break;
            	case "http-status-code":
            		$this->httpStatusCode($notification);
            		break;
       			default:
        			break;
            }
        
        }
    
    	if (count($this->messages) > 0) {
        	$this->sendMessage(implode("\r\n\r\n", $this->messages));
        	$this->messages = [];
        }
    	
    }

	public function httpStatusCode($notification) {
    
    	try {
    		$website = json_decode($notification->target)->website;
        	$expression = json_decode($notification->target)->expression;
    		$timeout = 5;
    	
    		$headers = @get_headers($website, 0);
			
        	if (!is_array($headers)) {
            	$statusCode = 0;
            } else {
				$statusCode = @substr($headers[0], 9, 3);
            }
        
        	$unit = "";
    	
    		$value = $statusCode;
        	
        	if ($this->evaluateExpression($expression, $value) == true) {
        		if ($notification->triggered == 1) {
        			$notification->triggered = 0;
    	   	 		$notification->last_value = $value;
        			$notification->save();
            		$description = __('all.notification_center.status_changed_with_value', ['notification' => $notification->alias, 'status' => mb_strtoupper(__('all.notification_center.idle'), 'UTF-8'), 'value' => $value . $unit ] );
            		$this->newNotificationLog($notification->id, 0, "status changed", $description);
           		
            		$this->messages[] = $description;
            	
           		}
        	} else {
        		if ($notification->triggered == 0) {
        			$notification->triggered = 1;
            		$notification->last_value = $value;
        			$notification->save();
            	
            		$description = __('all.notification_center.status_changed_with_value', ['notification' => $notification->alias, 'status' => mb_strtoupper(__('all.notification_center.alert'), 'UTF-8'), 'value' => $value . $unit ] );
            		$this->newNotificationLog($notification->id, 1, "status changed", $description);
            
            		$this->messages[] = $description;
            
            	}
        	}
        } catch(\Exception $ex2) {
        	\Log::info($ex2);
        }
    
    }

	public function ping($notification) {
    
    	try {
    	
        	$ip = $notification->target;
        	
        	$pingresult = exec("/bin/ping -c 7 $ip", $outcome, $status);
        	
        	if($status) {
            	if($notification->triggered == 0) {
                	$notification->triggered = 1;
            		$notification->save();
            	
            		$description = __('all.notification_center.status_changed', ['notification' => $notification->alias, 'status' => mb_strtoupper(__('all.notification_center.alert'), 'UTF-8') ] );;
                	$this->newNotificationLog($notification->id, 1, "status changed", $description);
                	$this->newNotificationLog($notification->id, NULL, "post event action", "<pre style='white-space: pre-wrap;'>" . implode("<br>", $outcome) . "</pre>");
                	
            		$this->messages[] = $description;
                
                }
            
            } else {
            
            	if($notification->triggered == 1) {
                	$notification->triggered = 0;
            		$notification->save();
            	
            		$description = __('all.notification_center.status_changed', ['notification' => $notification->alias, 'status' => mb_strtoupper(__('all.notification_center.idle'), 'UTF-8') ] ); 
            		$this->newNotificationLog($notification->id, 0, "status changed", $description);
                
                	$this->messages[] = $description;
                
                }
            
            }
        	
        } catch(\Exception $ex2) {
        	\Log::info($ex2);
        }
    
    }

	public function socketPolling($notification) {
    
    	$target = explode(":", $notification->target);
        $ip = $target[0];
        $port = $target[1];
        $timeout = 5;
    	
    	$socket = @fsockopen($ip, $port, $errno, $errstr, $timeout);
		
    	if (!$socket) {
        	sleep(10);
        	$socket = @fsockopen($ip, $port, $errno, $errstr, $timeout);
        }
    	
    	if ($socket) {
    		if ($notification->triggered == 1) {
            	$notification->triggered = 0;
            	$notification->last_value = NULL;
            	$notification->save();
            	
            	$description = __('all.notification_center.status_changed', ['notification' => $notification->alias, 'status' => mb_strtoupper(__('all.notification_center.idle'), 'UTF-8') ] );
            	$this->newNotificationLog($notification->id, 0, "status changed", $description);
            
            	$this->messages[] = $description;
                	
            }
    		fclose($socket);
		} else {
        	if ($notification->triggered == 0) {
				$notification->triggered = 1;
            	$notification->last_value = $errno . ", " . $errstr;
            	$notification->save();
            	
            	$description = __('all.notification_center.status_changed', ['notification' => $notification->alias, 'status' => mb_strtoupper(__('all.notification_center.alert'), 'UTF-8') ] ) .  " (" . $errno . ", " . $errstr . ")";
            	$this->newNotificationLog($notification->id, 1, "status changed", $description);
            
            	$this->messages[] = $description;
                	
            	$this->runNmap($notification);	
            }
        }
    
    }

	public function massHeartbeatLoss($notification) {
    	
    	$heartbeatLossList = Workstations::heartBeatLoss()->get();
    	$heartbeatLossCount = $heartbeatLossList->count();
    	$target = Notifications::where("type", "mass-heartbeat-loss")->first()->target;	
    
    	$count = 0;
    	$list = array();
        if ($heartbeatLossCount > 0) {
        	$first = $heartbeatLossList->first()->heartbeat;
    		$maxTime = Carbon::parse($first)->addSeconds(122);
    		foreach($heartbeatLossList as $ws) {
        		$current = Carbon::parse($ws->heartbeat);
    	    	if ($current <= $maxTime) {
               		$count = $count + 1;
                    $list[] = $ws->alias;
                } else {
                	if ($count < $target) {
                		$count = 1;
                    	unset($list);
                	    $list[] = $ws->alias;
                    	$maxTime = $current->addSeconds(122);
                    }
                    
                }
            }
        }
    
    	if ($count >= $target) {
        	if ($notification->triggered == 0) {
        		$notification->triggered = 1;
            	$notification->last_value = $count;
        		$notification->save();
            	
            	$description = __('all.notification_center.status_changed_with_value', ['notification' => $notification->alias, 'status' => mb_strtoupper(__('all.notification_center.alert'), 'UTF-8'), 'value' => $count ] );
            	$this->newNotificationLog($notification->id, 1, "status changed", $description);
            	sleep(1);
            	$this->newNotificationLog($notification->id, NULL, "post event action", implode(", ", $list));
            
            	$this->messages[] = $description;
                $this->messages[] = implode(", ", $list);
            }
        
        } else {
        	if ($notification->triggered == 1) {
        		$notification->triggered = 0;
            	$notification->last_value = NULL;
            	$notification->save();
            	
            	$description = __('all.notification_center.status_changed', ['notification' => $notification->alias, 'status' => mb_strtoupper(__('all.notification_center.idle'), 'UTF-8') ] );
            	$this->newNotificationLog($notification->id, 0, "status changed", $description);
            
				$this->messages[] = $description;
            }
        }
    	
    }

	public function checkSensorValue($notification) {
    
    	$value = $notification->last_value;
    	$unit = (isset($notification->unit))?$notification->unit:"";
    	$expression = $notification->target;
    	if ($this->evaluateExpression($expression, $value) == true) {
        	if ($notification->triggered == 1) {
        		$notification->triggered = 0;
            	$notification->save();
            	
            	$description = __('all.notification_center.status_changed_with_value', ['notification' => $notification->alias, 'status' => mb_strtoupper(__('all.notification_center.idle'), 'UTF-8'), 'value' => $value . $unit ] );
            	$this->newNotificationLog($notification->id, 0, "status changed", $description);
           		
            	$this->messages[] = $description;
            }
        } else {
        	if ($notification->triggered == 0) {
        		$notification->triggered = 1;
            	$notification->save();
            	
            	$description = __('all.notification_center.status_changed_with_value', ['notification' => $notification->alias, 'status' => mb_strtoupper(__('all.notification_center.alert'), 'UTF-8'), 'value' => $value . $unit ] );
            	$this->newNotificationLog($notification->id, 1, "status changed", $description);
            
            	$this->messages[] = $description;
            }
        }
    
    }

	public function biglanCommand($notification) {
    	
    	$wsid = json_decode($notification->target)->wsid;
    	$command = json_decode($notification->target)->command;
        $expression = json_decode($notification->target)->expression;
    	$value = $this->command($wsid, $command);
        $value = strip_tags(trim($value));
    	$notification->last_value = $value;
    	
    	if ($this->evaluateExpression($expression, $value) == true) {
        	if ($notification->triggered == 1) {
        		$notification->triggered = 0;
            	
            	
            	$description = __('all.notification_center.status_changed', ['notification' => $notification->alias, 'status' => mb_strtoupper(__('all.notification_center.idle'), 'UTF-8') ] );
            	$this->newNotificationLog($notification->id, 0, "status changed", $description);
            
            	$this->messages[] = $description;
            }
        } else {
        	if ($notification->triggered == 0) {
        		$notification->triggered = 1;
            	
            	$description = __('all.notification_center.status_changed', ['notification' => $notification->alias, 'status' => mb_strtoupper(__('all.notification_center.alert'), 'UTF-8') ] );
            	$this->newNotificationLog($notification->id, 1, "status changed", $description);
            
            	$this->messages[] = $description;
            }
        }
    
    	$notification->save();
    
    }
	
	public function snmpQuery($ip, $oid) {
    
    		
    		$snmpReadCommunity = GlobalSettings::where("name", "snmp-read-community")->first()->value;
    
    		if(!isset($snmpReadCommunity)) {
            	$snmpReadCommunity = "public";
            }
    		
    		snmp_set_valueretrieval(SNMP_VALUE_PLAIN);
            
    		$value = @snmpget($ip, $snmpReadCommunity, $oid, 50000);

    		return $value;
    
    }	

	public function snmp($notification) {
    	
    	$ip = json_decode($notification->target)->ip;
    	$oid = json_decode($notification->target)->oid;
        $expression = json_decode($notification->target)->expression;
    	$value = $this->snmpQuery($ip, $oid);
    	$value = strip_tags(trim($value));
    	$notification->last_value = $value;
    	
    	if ($this->evaluateExpression($expression, $value) == true) {
        	if ($notification->triggered == 1) {
        		$notification->triggered = 0;
            	$notification->save();
            	
            	$description = __('all.notification_center.status_changed', ['notification' => $notification->alias, 'status' => mb_strtoupper(__('all.notification_center.idle'), 'UTF-8') ] );
            	$this->newNotificationLog($notification->id, 0, "status changed", $description);
            
            	$this->messages[] = $description;
            }
        } else {
        	if ($notification->triggered == 0) {
        		$notification->triggered = 1;
            	$notification->save();
            	
            	$description = __('all.notification_center.status_changed', ['notification' => $notification->alias, 'status' => mb_strtoupper(__('all.notification_center.alert'), 'UTF-8') ] );
            	$this->newNotificationLog($notification->id, 1, "status changed", $description);
            
            	$this->messages[] = $description;
            }
        }
    
    	$notification->save();
    
    }


	public function command($id, $command)
    {
    
    	$request = [];
    	$request["id"] = $id;
    	$request["command"] = $command;
    	$run = new WorkstationsController();
    
    	return $run->command($request, false);
    
    }

	public function evaluateExpression($expression, $value) {
    	if (!is_numeric($value)) {$value = "'$value'";}
    	$expression = str_replace("value", "$value", $expression);
    	return eval("return ". $expression . ";");
	}

	public function runNmap($notification) {
    	
    	$target = explode(":", $notification->target);
		$ip = $target[0];
    	$port = $target[1];
    	$arguments = "-p " . $port . " -T4 -A -v";

		$command = "nmap " . escapeshellarg($ip) . " " . $arguments;

		$output = shell_exec($command);

    	$description = "<pre style='white-space: pre-wrap;'>Nmap:\n" . $output . "</pre>";
    
    	$this->newNotificationLog($notification->id, NULL, "post event action", $description);
    
    }

	function runTraceroute($notification) {
    	
    	$ip = $notification->target;
    	$command = "traceroute $ip";

    	exec($command, $output, $returnVar);

    	if ($returnVar === 0) {
        	$description = "<pre>" . implode("\n", $output) . "</pre>";
    	} else {
        	$description = "Traceroute failed. Error code: $returnVar";
    	}
    	
    	$this->newNotificationLog($notification->id, NULL, "post event action", $description);
    	
	}

	public function newNotificationLog($id, $status, $event, $description) {
    
    	$log = new NotificationLogs();
    	$log->notification_id = $id;
    	$log->status = $status;
        $log->event = $event;
    	$log->description = $description;
        $log->save();
    
    }

	public function sendMessage($message) {
    	$enableNotifications = (int)GlobalSettings::where("name", "enable-notifications")->first()->value;
    	
    	if ($enableNotifications === 1) {
    	
        	$telegramBotToken = GlobalSettings::where("name", "telegram-bot-token")->first()->value; 
    		$telegramChatId = GlobalSettings::where("name", "telegram-chat-id")->first()->value;
        
    		@file_get_contents("https://api.telegram.org:443/bot" . $telegramBotToken . "/sendMessage?chat_id=" . $telegramChatId . "&text=-----BigLan-----%0A%0A" . urlencode($message));
    
        }
    
    }

}