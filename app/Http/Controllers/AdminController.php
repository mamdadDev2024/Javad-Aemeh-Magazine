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
        ToastMagic::success('انجام شد', 'حذف با موفقیت انجام شد!' , ['positionClass' => 'toast-top-end']);
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
        ToastMagic::success('انجام شد', 'تغییرات با موفقیت ذخیره شد.');
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
            ToastMagic::error("خطا!" , "عملیات انجام نشد. لطفا دوباره تلاش کنید.");
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
            return ToastMagic::success("انجام شد", "مقام کاربر $user->username با موفقیت به $request->role تغییر کرد");
        } catch (\Exception $e) {
            Log::error("in changing user role => " . $e->getMessage());
            return ToastMagic::error("خطا", "در فرآیند مورد نظر مشکلی به وجود آمد!");
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
            return ToastMagic::error('خطا', 'شما مجوز لازم برای این عملیات را ندارید');
        }
        Comment::where('status', 0)->update(['status' => 1]);
        return ToastMagic::success('انجام شد', 'همه‌ی کامنت‌ها تایید شدند');
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
        $roles = Role::where("name", "!=", "super admin")->get(["name", "id"]);
        $users = User::where("username", "!=", "admin")->paginate(10);
        return view("admin-panel.users", compact("users", "roles"));
    }


    public function toggleStatus($id)
    {
        try {
            $user = User::findOrFail($id);
            $user->status = !$user->status;
            $user->save();

            return ToastMagic::success('انجام شد', 'وضعیت کاربر با موفقیت تغییر یافت');
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return ToastMagic::error('خطا', 'تغییر وضعیت کاربر ناموفق بود');
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
        $notConfirmedComments = Comment::where("status", false)->with(["user" , 'commentable'])->paginate(40);

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
            return ToastMagic::success('موفقیت', $successMessage);
        } catch (\Throwable $e) {
            Log::error("Failed to create {$model} => " . $e->getMessage());
            return ToastMagic::error('خطا', $errorMessage);
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
            return ToastMagic::success("انجام شد", "مورد با موفقیت تایید شد");
        }
        return ToastMagic::error("خطا", "خطا در تایید مورد");
    }

    private function deleteModel($modelClass, $id)
    {
        $model = $modelClass::find($id);
        if ($this->hasAdminRole() && $model) {
            $model->delete();
            return ToastMagic::success("انجام شد", "مورد با موفقیت حذف شد");
        }
        return ToastMagic::error("خطا", "خطا در حذف مورد");
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
            return ToastMagic::success('انجام شد', 'حذف کاربر موفقیت آمیز بود');
        } else {
            return ToastMagic::error('خطا', 'کاربر مورد نظر پیدا نشد');
        }
    }
}
