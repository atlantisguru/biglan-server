<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Http\Response;
use App\Models\Articles;
use App\Models\ArticleCategories;
use App\Models\ArticleCategoryRelations;
use App\Models\ArticleVersions;
use App\Models\ArticleComments;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ArticlesController extends Controller
{
    /*
     * Processing the incoming payloads based on "action" (Knowledge Base) 
     */
	public function payload(Request $request) {
    
    	if (isset($request["action"])) {
			
			$action = $request["action"];
			
			switch($action){
				
				case "saveArticle":
            		return $this->saveArticle($request);
            		break;
            	case "saveCategory":
            		return $this->saveCategory($request);
            		break;
            	case "saveComment":
            		return $this->saveComment($request);
            		break;
            	default:
					return null;
					break;
			}
		}	

    }


	/*
     * Gives back a list of Articles (Knowledge Base) 
     */
	public function listArticles(Request $request) {
    	
    	if(!auth()->user()->hasPermission('read-articles')) { return redirect('dashboard'); }	
    
    	if($request["q"] !== null) {
        	$q = $request["q"];
        } else {
        	$q = "";
        }
    
    	if($request["cat"] !== null) {
        	$cat = $request["cat"];
        	$article_array = ArticleCategoryRelations::select("article_id")->where("category_id","=", $cat)->pluck("article_id")->toArray();
        }
    	
    	$articles = Articles::where(function($query) use ($q) {
						$query->where("title", "LIKE", "%".$q."%")->orWhere("body", "LIKE", "%".$q."%" );				
					})->orderBy("updated_at", "DESC");
    	
    	
    
    	if($request["cat"] !== null) {
        	$articles->whereIn("id", $article_array);
        }
    
    	$articles = $articles->paginate(20);
        
    	$categories = ArticleCategoryRelations::selectRaw("COUNT(article_category_relations.id) AS count, article_category_relations.category_id as id, article_categories.name as name")->leftJoin('article_categories', function($join) {
      						$join->on('article_categories.id', '=', 'article_category_relations.category_id');
    					})->groupBy("article_category_relations.category_id")->orderBy("count", "DESC")->take(30)->get();
    
    	return view("articles.list", compact("articles", "categories"));
    }	
	
	/*
     * Gives back a specific article (Knowledge Base) 
     */
	public function getArticle($id) {
    
    	if(!auth()->user()->hasPermission('read-post')) { return redirect('dashboard'); }	
    	
    
    	$article = Articles::where("id", $id)->first();
    	return view("articles.article", compact("article"));
    }	

	/*
     * Displays a form to create a new article (Knowledge Base/New) 
     */
	public function newArticle() {
    	
    	if(!auth()->user()->hasPermission('write-post')) { return redirect('dashboard'); }	
    
    	$categories = ArticleCategories::orderBy("name", "ASC")->get();
    	return view("articles.edit", compact("categories"));
    }

	/*
     * Displays a form to edit an article (Knowledge Base/Edit) 
     */
	public function editArticle($id) {
    	
    	if(!auth()->user()->hasPermission('write-post')) { return redirect('dashboard'); }	
    
    	$article = Articles::where("id", $id)->first();
    	$categories = ArticleCategories::orderBy("name", "ASC")->get();
    	return view("articles.edit", compact("article", "categories"));
    }

	/*
     * Saves the article (Knowledge Base/Save) 
     */
	public function saveArticle($request) {
    	
    	if(!auth()->user()->hasPermission('write-post')) { return redirect('dashboard'); }	
    
   		$errors = array();
    
    	if (isset($request["artid"])) {
        	if ($request["artid"] == "") {
        		$article = new Articles();
            } else {
        		$artid = $request["artid"];
        		$article = Articles::where("id", $artid)->first();
            	if ($article == null) {
                	$errors[] = "Bejegyzés nem található";
                	return response()->json(['status' => 'error', 'errors' => $errors]);
                } else {
                	//mentese a cikk jelenlegi változatát
                	$archive = new ArticleVersions();
                	$archive->article_id = $article->id;
                	$archive->title = $article->title;
                	$archive->body = $article->body;
                	$archive->user_id = Auth::user()->id;
                	$archive->version_num = $article->version_num;
                	$archive->save();
                }
            }
        }
    
    	if(isset($request["title"])) {
        	if($request["title"] == "") {
            	$errors[] = "A cím nincs kitöltve";
            }
        }
    
    	if(isset($request["body"])) {
        	if($request["body"] == "") {
            	$errors[] = "Nincs leírás";
            }
        }
    
    	if(isset($request["categories"])) {
        	if($request["categories"] == "") {
            	$errors[] = "Nincs kategória kiválasztva";
            }
        } else {
           	$errors[] = "Nincs kategória kiválasztva";
         }
    
    	if (count($errors) > 0) {
        	return response()->json(['status' => 'error', 'errors' => $errors]);
        }
    
    	$article->title = $request["title"];
    	$article->body = $request["body"];
    	if ($request["artid"] == "") {
    		$article->version_num = 1;
        } else {
        	$article->version_num = $article->version_num + 1;
        }
    	$article->user_id = Auth::user()->id;
    	$save = $article->save();
    
    	if (!$save) {
        	$errors[] = "Nem sikerült menteni a bejegyzést";
        	return response()->json(['status' => 'error', 'errors' => $errors]);
        } else {
        	$artid = $article->id;
        }
    
    	$categories = explode(",", $request["categories"]);
    	
    	$oldCategories = ArticleCategoryRelations::where("article_id", $article->id)->pluck("category_id")->toArray();
        
        
    	foreach($categories as $category) {
        	if (!in_array($category, $oldCategories)) {
            	$newArtCat = new ArticleCategoryRelations();
            	$newArtCat->article_id = $article->id;
            	$newArtCat->category_id = $category;
            	$newArtCat->save();
            } else {
            	if (($key = array_search($category, $oldCategories)) !== false) {
 				   unset($oldCategories[$key]);
				}
            }
        }
    	
            		
    
    	$leftOverCats = ArticleCategoryRelations::where('article_id', $article->id)->whereIn('category_id', $oldCategories);
    	$leftOverCats->delete();
    
    	return response()->json(['status' => 'ok', 'artid' => $article->id]);
    
    }
	
	/*
     * Saves a new Category for Articles (Knowledge Base) 
     */
	public function saveCategory($request) {
    	
    	if(!auth()->user()->hasPermission('write-post')) { return "ERROR"; }	
    
    	$errors = array();
    
        $category = new ArticleCategories();
         
    	if(isset($request["name"])) {
        	if($request["name"] == "") {
            	$errors[] = "Nincs név";
            } else {
            	$exists = ArticleCategories::where("name", $request["name"])->first();
            	if ($exists != null) { 
            		return response()->json(['status' => 'exists', 'catid' => $exists->id]);
                }
            }
        	
        }
    	
    	if (count($errors) > 0) {
        	return response()->json(['status' => 'error', 'errors' => $errors]);
        }
    
    	$category->name = $request["name"];
    	$category->parent_id = 0;
    	$save = $category->save();
    	
    	if (!$save) {
        	$errors[] = "Nem sikerült menteni";
        	return response()->json(['status' => 'error', 'errors' => $errors]);
        } else {
        	$catid = $category->id;
        }
    
    	return response()->json(['status' => 'ok', 'catid' => $catid, 'catname' => $category->name]);
    
    }

	/*
     * Saves a Comment for an Article (Knowledge Base) 
     */
	public function saveComment($request) {
    	
    	if(!auth()->user()->hasPermission('write-comment')) { return "ERROR"; }	
    
    	$errors = array();
    
        $comment = new ArticleComments();
         
    	if(isset($request["comment"])) {
        	if($request["comment"] == "") {
            	$errors[] = "Írj valamit a hozzászólásodban!";
            }        	
        }
    	
    	if (count($errors) > 0) {
        	return response()->json(['status' => 'error', 'errors' => $errors]);
        }
    
    	$comment->article_id = $request["article_id"];
    	$comment->user_id = Auth::user()->id;
        $comment->comment = $request["comment"];
    	$save = $comment->save();
    	
    	if (!$save) {
        	$errors[] = "Nem sikerült menteni a hozzászólásod";
        	return response()->json(['status' => 'error', 'errors' => $errors]);
        }
    
    	return response()->json(['status' => 'ok']);
    
    }

	/*
     * It should be a file or image upload for an Article (Knowledge Base)
     * TODO: make it work 
     */
	public function upload(Request $request) {
    
    // JSON adat lekérése
    $data = $request->json()->all();
    $base64File = $data['file'] ?? null;
    $originalName = $data['originalName'] ?? null;

    if (!$base64File || !$originalName) {
        return response()->json(['error' => 'Érvénytelen fájl'], 400);
    }

    // A fájl kiterjesztésének kinyerése
    $extension = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));

    // Képfájlok ellenőrzése
    $imageExtensions = ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'svg', 'webp'];
    $isImage = in_array($extension, $imageExtensions);

    // Mappa kiválasztása
    $directory = $isImage ? storage_path('articles/images') : storage_path('articles/files');

    // A base64 fájl dekódolása
    $file = base64_decode($base64File);

    // Új fájlnév generálása
    $filename = time() . '_' . $originalName;
    $path = $directory . '/' . $filename;

    // Fájl mentése
    file_put_contents($path, $file);

    // URL generálása
    $publicPath = $isImage ? "articles/images/$filename" : "articles/files/$filename";
    $url = asset($publicPath);

    return response()->json([
        'url' => $url,
        'isImage' => $isImage,
        'filename' => $originalName,
    ]);
	
    }

	/*
     * Gives back a file path for an Article (Knowledge Base) 
     */
	public function getFile($filename) {
	
    	$path = storage_path('articles/files/' . $filename);

    	if (!File::exists($path)) {
			abort(404);
    	}
	
    	$file = File::get($path);
    	$type = File::mimeType($path);

    	$response = response($file, 200);
    	$response->header("Content-Type", $type);

    	return $response;
	
    }

	/*
     * Gives back an image path for an Article (Knowledge Base) 
     */
	public function getImage($filename) {
	
    	$path = storage_path('articles/images/' . $filename);

    	if (!File::exists($path)) {
    		abort(404);
    	}

    	$file = File::get($path);
    	$type = File::mimeType($path);

    	$response = response($file, 200);
    	$response->header("Content-Type", $type);

    	return $response;
	}

}