<?php

namespace App\Http\Controllers;

use App\Http\Requests\Admin\UpdateSettingRequest;
use App\Http\Requests\Admin\UpdateUserRoleStatusRequest;
use App\Models\{Article, Category, Comment, Contact, Event, Khabar, Link, Magazine, Recommend, Role, Scope, Section, User};
use Devrabiul\ToastMagic\Facades\ToastMagic;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\{Auth, Log, Validator};

class AdminController extends Controller
{
    public function deleteLink(Request $request){
        $data = $request->validate([
            "id" => "exists:links,id"
        ]);
        $link = Link::find($data["id"]);
        $link->delete();
        ToastMagic::warning('انجام شد', 'حذف با موفقیت انجام شد!');
        return redirect()->back();
    }
    public function updateAll(UpdateSettingRequest $request)
    {
        $request->validated();

        if ($request->filled('new_category')) {
            Category::create(['name' => $request->input('new_category')]);
        }

        if ($request->filled('new_scope')) {
            Scope::create(['name' => $request->input('new_scope')]);
        }

        foreach (Section::all() as $section) {
            if ($request->hasFile('titleHeader') && $section->name == 'titleHeader') {
                $file = $request->file('titleHeader');
                $path = $file->store('images', 'public');
                $section->update(['content' => $path]);
            } elseif ($request->hasFile('titleFooter') && $section->name == 'titleFooter') {
                $file = $request->file('titleFooter');
                $path = $file->store('images', 'public');
                $section->update(['content' => $path]);
            } elseif ($request->hasFile('defaultContentImage') && $section->name == 'defaultContentImage') {
                $file = $request->file('defaultContentImage');
                $path = $file->store('images', 'public');
                $section->update(['content' => $path]);
            }


            if ($request->filled($section->name)) {
                $section->update(['content' => $request->input($section->name)]);
            }
        }
        if ($request->filled("linkName") && $request->filled("link")) {
            Link::create([
                "name" => $request["linkName"],
                "link" => $request["link"]
            ]);
        }
        ToastMagic::warning('انجام شد', 'تغییرات با موفقیت ذخیره شد.');
        return redirect()->back();
    }

    public function updateUsers(UpdateUserRoleStatusRequest $request)
    {
        $validated = $request->validated();

        try {
            foreach ($validated['statuses'] as $userId => $status) {
                User::where('id', $userId)->update(['status' => $status]);
            }
            foreach ($validated['roles'] as $userId => $roleId) {
                $user = User::find($userId);
                if ($user) {
                    $user->roles()->sync([$roleId]);
                }
            }
            ToastMagic::success("انجام شد" , "تغییرات با موفقیت انجام شدند!");
            return redirect()->route("admin.index_users");
        } catch (\Exception $e) {
            Log::error("Error updating users: " . $e->getMessage());
            ToastMagic::success("خطا!" , "عملیات انجام نشد. لطفا دوباره تلاش کنید.");
            return redirect()->back();
        }
    }


    public function changeRole(Request $request)
    {
        $data = $request->validate([
            'g-recaptcha-response' => 'required|captcha',
            "role" => "required|in:admin,user|string",
            "user" => "required|exists:users,id"
        ]);
        try {
            $user = User::find($data["user"]);
            $user->syncRoles($data["role"]);
            return $this->setFlashMessage("انجام شد", "مقام کاربر $user->username با موفقیت به $request->role تغییر کرد");
        } catch (\Exception $e) {
            Log::error("in changing user role => " . $e->getMessage());
            return $this->setFlashMessage("خطا", "در فرآیند مورد نظر مشکلی به وجود آمد!", "error");
        }
    }

    public function indexContacts()
    {
        $contacts = Contact::with("user")->paginate(10);
        return view("admin-panel.contacts", compact("contacts"));
    }

    public function panel()
    {
        $scopes = Scope::all()->keyBy("name") ?? null;
        $categories = Category::all()->keyBy("name") ?? null;
        $sections = Section::all()->keyBy("name") ?? null;
        $links = Link::all()->toArray() ?? null;
        return view("admin-panel.panel", compact("sections", "scopes", "categories" , "links"));
    }

    public function approveAllComments()
    {
        if (!$this->hasAdminRole()) {
            return $this->setFlashMessage('خطا', 'شما مجوز لازم برای این عملیات را ندارید', 'error');
        }
        Comment::where('status', 0)->update(['status' => 1]);
        return $this->setFlashMessage('انجام شد', 'همه‌ی کامنت‌ها تایید شدند', 'success');
    }

    public function acceptComment($id)
    {
        return $this->acceptModel(Comment::class, $id);
    }

    public function deleteMagazine($id)
    {
        return $this->deleteModel(Magazine::class, $id);
    }

    public function deleteRecommend($id)
    {
        return $this->deleteModel(Recommend::class, $id);
    }

    public function deleteComment($id)
    {
        return $this->deleteModel(Comment::class, $id);
    }

    public function approveAllPosts()
    {
        if (!$this->hasAdminRole()) {
            return $this->setFlashMessage('خطا', 'شما مجوز لازم برای این عملیات را ندارید', 'error');
        }

        Article::where('status', 0)->update(['status' => 1]);
        Khabar::where('status', 0)->update(['status' => 1]);
        Event::where('status', 0)->update(['status' => 1]);

        return $this->setFlashMessage('انجام شد', 'همه‌ی محتوا ها تایید شدند', 'success');
    }

    public function acceptArticle($id)
    {
        return $this->acceptModel(Article::class, $id);
    }

    public function deleteEvent($id)
    {
        return $this->deleteModel(Event::class, $id);
    }

    public function deleteNew($id)
    {
        return $this->deleteModel(Khabar::class, $id);
    }

    public function acceptEvent($id)
    {
        return $this->acceptModel(Event::class, $id);
    }

    public function acceptNews($id)
    {
        return $this->acceptModel(Khabar::class, $id);
    }

    public function userDestroy($id)
    {
        $user = Auth::user();
        return $this->destroyUserById($id, $user);
    }

    public function indexUsers()
    {
        $roles = Role::where("name", "!=", "super admin")->where("name", "!=", "programmer")->get(["name", "id"]);
        $users = User::where("username", "!=", "admin")->where("username", "!=", "mohammadhoseinhashemi")->paginate(10);
        return view("admin-panel.users", compact("users", "roles"));
    }


    public function toggleStatus($id)
    {
        try {
            $user = User::findOrFail($id);
            $user->status = !$user->status;
            $user->save();

            return $this->setFlashMessage('انجام شد', 'وضعیت کاربر با موفقیت تغییر یافت', 'success');
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return $this->setFlashMessage('خطا', 'تغییر وضعیت کاربر ناموفق بود', 'error');
        }
    }

    public function indexContents()
    {
        $Magazines = Magazine::with("user")->withCount("articles as articles_count")->paginate(20);
        $Events = Event::with("user")->paginate(20);
        $News = Khabar::with("user")->paginate(20);
        $Recommends = Recommend::with("user")->paginate(20);
        return view("admin-panel.posts", compact("Magazines", "Events", "News", "Recommends"));
    }

    public function destroyUser($id)
    {
        $user = Auth::user();
        return $this->destroyUserById($id, $user);
    }

    public function showComment($id)
    {
        $comment = Comment::findOrFail($id);
        return view("admin-panel.show.comment", compact("comment"));
    }

    public function userDetail($id)
    {
        $user = User::findOrFail($id);
        return view("admin-panel.show.user", compact("user"));
    }

    public function suggestDetail($id)
    {
        $suggest = Article::findOrFail($id);
        return view("admin-panel.show.suggest", compact("suggest"));
    }

    public function articleDetail($title)
    {
        $article = Article::where("title", $title)->firstOrFail();
        return view("admin-panel.show.article", compact("article"));
    }

    public function newsDetail($title)
    {
        $new = Khabar::where("title", $title)->firstOrFail();
        return view("admin-panel.show.new", compact("new"));
    }

    public function eventDetail($title)
    {
        $event = Event::where("title", $title)->firstOrFail();
        return view("admin-panel.show.event", compact("event"));
    }

    public function indexComments()
    {
        $notConfirmedComments = Comment::where("status", false)->with("user", "commentable")->paginate(10);
        return view("admin-panel.comments", compact("notConfirmedComments"));
    }

    public function contactDelete($id){
        $contact = Contact::class;
        return $this->deleteModel($contact , $id);
    }

    public function createCategory(Request $request)
    {
        return $this->createModel($request, Category::class, 'دسته‌بندی با موفقیت ایجاد شد', 'خطا در ایجاد دسته‌بندی');
    }
    protected function setFlashMessage($title, $message, $type = 'success')
    {
        ToastMagic::warning($title, $message, $type);
        return back();
    }
    public function createScope(Request $request)
    {
        return $this->createModel($request, Scope::class, 'سطح با موفقیت ایجاد شد', 'خطا در ایجاد سطح');
    }



    function createModel(Request $request, string $model, string $successMessage, string $errorMessage, string $uniqueColumn = 'name')
    {
        $data = $request->validate([
            "g-response-captcha" => "required|captcha",
            $uniqueColumn => "required|min:3|max:100|unique:{$model},{$uniqueColumn}",
        ]);

        try {
            $model::create($data);
            return $this->setFlashMessage('موفقیت', $successMessage, 'success');
        } catch (\Throwable $e) {
            Log::error("Failed to create {$model} => " . $e->getMessage());
            return $this->setFlashMessage('خطا', $errorMessage, 'error');
        }
    }

    private function hasAdminRole()
    {
        return Auth::user()->hasRole('admin|super admin');
    }

    private function acceptModel($modelClass, $id)
    {
        $model = $modelClass::find($id);
        if ($this->hasAdminRole() && $model && $model->status == 0) {
            $model->status = 1;
            $model->save();
            return $this->setFlashMessage("انجام شد", "مورد با موفقیت تایید شد");
        }
        return $this->setFlashMessage("خطا", "خطا در تایید مورد", "error");
    }

    private function deleteModel($modelClass, $id)
    {
        $model = $modelClass::find($id);
        if ($this->hasAdminRole() && $model) {
            $model->delete();
            return $this->setFlashMessage("انجام شد", "مورد با موفقیت حذف شد");
        }
        return $this->setFlashMessage("خطا", "خطا در حذف مورد", "error");
    }

    private function canDeleteUser($user, $currentUser = null)
    {
        $response = true;
        if (!$currentUser) {
            $response = false;
        }
        if ($user and $user->id == $currentUser->id and !$user->can("index user")) {
            $response = true;
        }
        if ($currentUser->hasRole("super admin") and $currentUser->id == $user->id) {
            $response = false;
        }
        return $response;
    }

    private function destroyUserById($id, $currentUser = null)
    {

        $user = User::find($id);
        if ($this->canDeleteUser($user, $currentUser)) {
            $user->delete();
            return $this->setFlashMessage('انجام شد', 'حذف کاربر موفقیت آمیز بود');
        } else {
            return $this->setFlashMessage('خطا', 'کاربر مورد نظر پیدا نشد', 'error');
        }
    }
}
