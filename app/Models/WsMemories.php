<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WsMemories extends Model
{
    public function type() {
    	$type = $this->type;
    	switch($type) {
        	case 20:
                return "DDR";
                break;
            case 21:
                return "DDR-2";
                break;
            case 17:
                return "SDRAM";
                break;
            default:
                if ($type == 0 || $type > 22)
                    return "DDR-3";
                else
                    return "Unknown";
        		break;
        }
    }
}
