<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\ApiController;
use App\Http\Controllers\EnvironmentController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\WorkstationsController;
use App\Http\Controllers\TopologyController;
use App\Http\Controllers\NetworkDevicesController;
use App\Http\Controllers\SubnetsController;
use App\Http\Controllers\NotificationsController;
use App\Http\Controllers\OperatingSystemsController;
use App\Http\Controllers\NetworkPrintersController;
use App\Http\Controllers\UsersController;
use App\Http\Controllers\GlobalSettingsController;
use App\Http\Controllers\DocumentsController;
use App\Http\Controllers\DownloadsController;
use App\Http\Controllers\UpdatesController;
use App\Http\Controllers\ArticlesController;
use App\Http\Controllers\CommandsController;
use App\Http\Controllers\ApiTokensController;

//AUTHENTICATION
Route::get('/login', [LoginController::class, 'viewLogin'])->name('login');
Route::post('/login', [LoginController::class, 'authenticate']);
Route::get("/logout", [LoginController::class, "logout"])->middleware("auth");
Route::get("/lostpassword", [ForgotPasswordController::class, "viewLostPassword"])->middleware("guest");
Route::post("/lostpassword", [ForgotPasswordController::class, "send"])->middleware("guest");
Route::get("/register", [RegisterController::class, "viewRegister"])->middleware("guest");
Route::post("/register", [RegisterController::class, "store"])->middleware("guest");
Route::get("/resetpassword/{token}", [ResetPasswordController::class, "check"])->middleware("guest");
Route::post("/resetpassword/{token}", [ResetPasswordController::class, "reset"])->middleware("guest");

//APIs
Route::any('/api/v2', [ApiController::class, 'payload']);
Route::post('/api/environment', [EnvironmentController::class, 'payload']);
Route::get('/api/printerstats/{offset}', [ApiController::class, 'convertPrints2Stats'])->middleware("auth");

//DASHBOARD
Route::get('/', [DashboardController::class, 'viewDashboard'])->name('dashboard')->middleware("auth");
Route::get('/dashboard', [DashboardController::class, 'viewDashboard'])->name('dashboard')->middleware("auth");
Route::post("/dashboard/payload", [DashboardController::class, 'payload'])->middleware("auth");

//WORKSTATIONS
Route::get("/workstations", [WorkstationsController::class, "listWorkstations"])->middleware("auth");
Route::post("/workstations/payload", [WorkstationsController::class, "payload"])->middleware("auth");
Route::get("/workstations/new", [WorkstationsController::class, "newWorkstation"])->middleware("auth");
Route::get("/workstations/displays", [WorkstationsController::class, "listDisplays"])->middleware("auth");
Route::get("/workstations/printers", [WorkstationsController::class, "listPrinters"])->middleware("auth");
Route::get("/workstations/vnc/{ip}", [WorkstationsController::class, "connectVNC"])->middleware("auth");
Route::get("/workstations/filter/keyword/{keyword}", [WorkstationsController::class, "listWorkstations"])->middleware("auth");
Route::get("/workstations/filter/{filter}", [WorkstationsController::class, "listWorkstations"])->middleware("auth");
Route::get("/workstations/createfilter", [WorkstationsController::class, "createFilter"])->middleware("auth");
Route::post("/workstations/save", [WorkstationsController::class, "saveWorkstation"])->middleware("auth");
Route::post("/workstations/savefilter", [WorkstationsController::class ,"saveFilter"])->middleware("auth");
Route::get("/workstations/{id}", [WorkstationsController::class, "getWorkstation"])->middleware("auth");

//TOPOLOGY
Route::get('/topology', [TopologyController::class, "viewTopology"])->middleware("auth");
Route::get('/topology/update', [TopologyController::class, "getUpdate"])->middleware("auth");
Route::post("/topology/payload", [TopologyController::class, "payload"])->middleware("auth");

//NETWORK DEVICES
Route::get('/networkdevices', [NetworkDevicesController::class, "listNetworkDevices"])->middleware("auth");
Route::get('/networkdevices/new', [NetworkDevicesController::class, "newNetworkDevice"])->middleware("auth");
Route::post('/networkdevices/save', [NetworkDevicesController::class, "saveNetworkDevice"])->middleware("auth");
Route::post("/networkdevices", [NetworkDevicesController::class, "payload"])->middleware("auth");

//SUBNETWORKS
Route::get('/subnets', [SubnetsController::class, "listSubnets"])->middleware("auth");
Route::get("/subnets/new", [SubnetsController::class, "newSubnet"])->middleware("auth");
Route::post("/subnets/payload", [SubnetsController::class, "payload"])->middleware("auth");
Route::post("/subnets/save", [SubnetsController::class, "createSubnet"])->middleware("auth");

//NOTIFICATIONS
Route::get('/notifications', [NotificationsController::class, "listNotifications"])->middleware("auth");
Route::get("/notifications/new", [NotificationsController::class, "newNotification"])->middleware("auth");
Route::get("/notifications/logs", [NotificationsController::class, "listNotificationLogs"])->middleware("auth");
Route::get("/notifications/dashboard", [NotificationsController::class, "showNotificationDashboard"])->middleware("auth");
Route::post("/notifications/save", [NotificationsController::class, "createNotification"])->middleware("auth");
Route::post("/notifications/payload", [NotificationsController::class, "payload"])->middleware("auth");

//OPERATING SYSTEMS
Route::get('/operatingsystems', [OperatingSystemsController::class, "listOperatingSystems"])->middleware("auth");
Route::post('/operatingsystems/payload', [OperatingSystemsController::class, "payload"])->middleware("auth");

//NETWORK PRINTERS
Route::get('/networkprinters', [NetworkPrintersController::class, "listNetworkPrinters"])->middleware("auth");
Route::get("/networkprinters/new", [NetworkPrintersController::class, "newNetworkPrinter"])->middleware("auth");
Route::post("/networkprinters/save", [NetworkPrintersController::class, "createNetworkPrinter"])->middleware("auth");
Route::post('/networkprinters/payload', [NetworkPrintersController::class, "payload"])->middleware("auth");
Route::post('/networkprinters/event', [NetworkPrintersController::class, "SNMPEvent"])->middleware("auth");

//USERS
Route::get("/users", [UsersController::class, "listUsers"])->middleware("auth");
Route::get("/users/permissions/{token}", [UsersController::class, "userPermissions"])->middleware("auth");
Route::get("/users/activities/{token}", [UsersController::class, "userActivities"])->middleware("auth");
Route::get("/users/status/{token}", [UsersController::class, "userStatus"])->middleware("auth");
Route::post("/users/savePermissions", [UsersController::class, "savePermissions"])->middleware("auth");

//USER SETTINGS
Route::get("/settings", [UsersController::class, "loadView"])->middleware("auth");
Route::post("/settings", [UsersController::class, "payload"])->middleware("auth");

//GLOBAL SETTINGS
Route::get('/globalsettings', [GlobalSettingsController::class, "listGlobalSettings"])->middleware("auth");
Route::post('/globalsettings/save', [GlobalSettingsController::class, "saveGlobalSettings"])->middleware("auth");
Route::get('/globalsettings/logs', [GlobalSettingsController::class, "listGlobalSettingsLogs"])->middleware("auth");

//DOCUMENTS
Route::get('/documents', [DocumentsController::class, "listDocuments"])->middleware("auth");
Route::post('/documents', [DocumentsController::class, "payload"])->middleware("auth");
Route::get('/documents/trash', [DocumentsController::class, "listTrashDocuments"])->middleware("auth");
Route::get('/documents/{filename}', [DocumentsController::class, "getFile"])->middleware("auth");

//ARTICLES
Route::get('/articles', [ArticlesController::class, "listArticles"])->middleware("auth");
Route::get('/articles/new', [ArticlesController::class, "newArticle"])->middleware("auth");
Route::get('/articles/edit/{id}', [ArticlesController::class, "editArticle"])->middleware("auth");
Route::get('/articles/article/{id}', [ArticlesController::class, "getArticle"])->middleware("auth"); 
Route::post('/articles/payload', [ArticlesController::class, "payload"])->middleware("auth"); 
Route::post('/articles/upload', [ArticlesController::class, "upload"])->middleware("auth"); 
Route::get('/articles/files/{filename?}', [ArticlesController::class, "getFile"])->where('filename', '(.*)')->middleware("auth");
Route::get('/articles/images/{filename?}', [ArticlesController::class, "getImage"])->where('filename', '(.*)')->middleware("auth");

//COMMAND CENTER
Route::get('/commands', [CommandsController::class, "listCommands"])->middleware("auth");
Route::post('/commands/payload', [CommandsController::class, "payload"])->middleware("auth");
Route::get('/commands/new', [CommandsController::class, "newCommand"])->middleware("auth");
Route::get('/commands/edit/{id}', [CommandsController::class, "editCommand"])->middleware("auth");
Route::get('/commands/command/{id}', [CommandsController::class, "viewCommand"])->middleware("auth");
Route::get('/commands/scripts', [CommandsController::class, "viewScripts"])->middleware("auth");

//DOWNLOADS
Route::get('/downloads', [DownloadsController::class, "viewDownloads"]);
Route::post('/downloads', [DownloadsController::class, "payload"])->middleware("auth");
Route::get('/downloads/{filename?}', [DownloadsController::class, "fileDownload"])->where('filename', '(.*)');

//UPDATES
Route::get('/updates', [UpdatesController::class, "viewUpdates"])->middleware("auth");
Route::get('/updates/{filename?}', [UpdatesController::class, "downloadUpdate"])->where('filename', '(.*)');
Route::post('/updates', [UpdatesController::class, "payload"])->middleware("auth");

Route::get("/about", function() { return view("about"); })->middleware("auth");
Route::get("/about-public", function() { return view("about_public"); })->middleware("guest");
Route::get("/help", function() { return view("help"); })->middleware("auth");

Route::get('/apitokens', [ApiTokensController::class, "listTokens"])->middleware("auth");
Route::get('/apitokens/new', [ApiTokensController::class, "newToken"])->middleware("auth");
Route::post('/apitokens/save', [ApiTokensController::class, "saveToken"])->middleware("auth");
Route::post('/apitokens/revoke/{id}', [ApiTokensController::class, "revokeToken"])->middleware("auth")->name("apitokens.revoke");


URL::forceScheme('https');