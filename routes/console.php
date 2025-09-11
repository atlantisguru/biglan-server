<?php

use Illuminate\Support\Facades\Schedule;

use App\Console\Commands\CheckHeartbeatLoss;
use App\Console\Commands\CheckCpuBenchmarks;
use App\Console\Commands\CheckNetworkPrinters;
use App\Console\Commands\CheckOperatingSystems;
use App\Console\Commands\NotificationMonitor;
use App\Console\Commands\CleanWsEvents;

Schedule::command('workstation:heartbeatloss')->everyMinute();
Schedule::command('notificationmonitor:check')->everyMinute();

Schedule::command('workstation:cleanevents')->dailyAt('04:00');
Schedule::command('networkprinters:check')->dailyAt('10:00');
Schedule::command('operatingsystems:check')->dailyAt('09:00');

Schedule::command('command:checkcpubenchmarks')->monthlyOn(2, '07:00');