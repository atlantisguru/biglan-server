<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('network_printers', function (Blueprint $table) {
            $table->id();
            $table->text('alias');
            $table->string('brand', 255)->nullable();
        	$table->string('ip', 255);
        	$table->text('mac')->nullable();
        	$table->text('serial')->nullable();
        	$table->string('inventory_id', 255)->nullable();
        	$table->text('notes')->nullable();
        	$table->integer('black_toner_max')->nullable();
        	$table->integer('black_toner_level')->nullable();
        	$table->integer('is_color_capable')->nullable();
        	$table->integer('cyan_toner_max')->nullable();
        	$table->integer('cyan_toner_level')->nullable();
        	$table->integer('magenta_toner_max')->nullable();
        	$table->integer('magenta_toner_level')->nullable();
        	$table->integer('yellow_toner_max')->nullable();
        	$table->integer('yellow_toner_level')->nullable();
        	$table->integer('print_counter')->nullable();
        	$table->timestamps();
        });
    
    	Schema::create('network_printer_events', function (Blueprint $table) {
            $table->id();
            $table->integer('printer_id');
        	$table->text('event');
        	$table->timestamps();
        });
    
    	Schema::create('network_printer_statistics', function (Blueprint $table) {
            $table->id();
            $table->integer('printer_id');
        	$table->integer('black_toner_level')->nullable();
        	$table->integer('cyan_toner_level')->nullable();
        	$table->integer('magenta_toner_level')->nullable();
        	$table->integer('yellow_toner_level')->nullable();
        	$table->integer('print_counter');
        	$table->timestamps();
        });
    
    	Schema::create('network_printer_supplies', function (Blueprint $table) {
            $table->id();
            $table->integer('printer_id');
        	$table->text('prtMarkerSuppliesType')->nullable();
        	$table->text('prtMarkerSuppliesDescription')->nullable();
        	$table->integer('prtMarkerSuppliesMaxCapacity');
        	$table->integer('prtMarkerSuppliesLevel');
        	$table->timestamps();
        });
    	
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('network_printers');
    	Schema::dropIfExists('network_printer_events');
    	Schema::dropIfExists('network_printer_statistics');
    	Schema::dropIfExists('network_printer_supplies');
    	
    }
};
