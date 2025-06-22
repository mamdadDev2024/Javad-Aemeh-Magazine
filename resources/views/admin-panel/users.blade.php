@extends('layouts.default')
@section('title', 'کاربران')
@section('body')
    <main class="p-6">
        <h1 class="text-2xl font-bold mb-4">لیست کاربران</h1>
        <form action="{{ route('admin.update.users') }}" method="POST" class="overflow-x-auto">
            @csrf
            @if ($errors->any())
                <div class="bg-red-500 mb-4 rounded-2xl p-3">
                    @foreach ($errors->all() as $error)
                        <div class="text-white list-item mr-2">
                            {{ $error }}
                        </div>
                    @endforeach
                </div>
            @endif

            <table class="w-full border-collapse border border-gray-300">
                <thead>
                    <tr>
                        <th class="border border-gray-300 p-2">نام کاربری</th>
                        <th class="border border-gray-300 p-2">ایمیل</th>
                        <th class="border border-gray-300 p-2">شماره تماس</th>
                        <th class="border border-gray-300 p-2">وضعیت</th>
                        <th class="border border-gray-300 p-2">مقام</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($users as $user)
                        @php
                            $user_role = $user->roles()->get()->toArray();
                        @endphp
                        <tr>
                            <td class="border border-gray-300 p-2">{{ $user->username }}</td>
                            <td class="border border-gray-300 p-2">{{ $user->number }}</td>
                            <td class="border border-gray-300 p-2">{{ $user->email }}</td>
                            <td class="border border-gray-300 p-2">
                                <select name="statuses[{{ $user->id }}]" class="form-select">
                                    <option value="1" {{ $user->status ? 'selected' : '' }}>فعال</option>
                                    <option value="0" {{ !$user->status ? 'selected' : '' }}>غیرفعال</option>
                                </select>
                            </td>
                            <td class="border border-gray-300 p-2">
                                <select name="roles[{{ $user->id }}]" class="form-select">
                                    @foreach ($roles as $role)
                                        <option value="{{ $role->id }}" {{ $role->id == $user_role[0]['id'] ? 'selected' : '' }}>
                                            {{ $role->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            <x-captcha/>
            <div class="mt-4">
                <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded">ذخیره تغییرات</button>
            </div>
        </form>

        <div class="mt-4 mx-auto max-w-lg">
            {{ $users->links() }}
        </div>
    </main>
@endsection
