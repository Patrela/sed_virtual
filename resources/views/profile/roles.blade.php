<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Profile') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <div class="max-w-xl">
                    <section class="space-y-6">
                        <header>
                            <header>
                                <h2 class="text-lg font-medium text-gray-900">
                                    {{ __('Profile Information') }}
                                </h2>

                                <p class="mt-1 text-sm text-gray-600">
                                    {{ __("Update account's profile information.") }}
                                </p>
                            </header>

                            <form id ="userProfileForm" class="mt-6 space-y-6">
                                @csrf
                                @method('PUT')
                                <div>
                                    <x-input-label for="email" :value="__('Email')" />
                                    <x-text-input id="email" name="email" type="email" class="mt-1 block w-full"
                                        :value="old('email', $user->email)" required autocomplete="username" />
                                    <x-secondary-button
                                        onclick="searchUserEmailProfile()">{{ __('Search user') }}</x-primary-button>

                                </div>

                                <div>
                                    <x-input-label for="name" :value="__('Name')" />
                                    <x-text-input id="name" name="name" type="text" class="mt-1 block w-full"
                                        :value="old('name', $user->name)" required autofocus autocomplete="name" disabled />
                                    <x-input-error class="mt-2" :messages="$errors->get('name')" />
                                </div>

                                <div>
                                    <x-input-label for="trade_id" :value="__('Trade Id')" />
                                    <x-text-input id="trade_id" name="trade_id" type="text"
                                        class="mt-1 block w-full" :value="old('trade_id', $user->trade_id)" required autofocus
                                        autocomplete="trade_id" disabled />
                                    <x-input-error class="mt-2" :messages="$errors->get('trade_id')" />
                                </div>

                                <div>
                                    <x-input-label for="role_type" :value="__('Role Type')" />
                                    <x-select name="role_type" :options="[
                                        1 => 'Administrator',
                                        2 => 'Staff',
                                        3 => 'Trade ',
                                        4 => 'Reseller ',
                                        5 => 'Support ',
                                        6 => 'Developer',
                                    ]" :selected="old('role_type', $user->role_type)">
                                    </x-select>
                                </div>

                                <div class="flex items-center gap-4">
                                    <x-primary-button>{{ __('Save') }}</x-primary-button>

                                    @if (session('status') === 'profile-updated')
                                        <p x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 2000)"
                                            class="text-sm text-gray-600">{{ __('Saved.') }}</p>
                                    @endif
                                </div>
                            </form>
                    </section>

                </div>
            </div>
        </div>
    </div>
    <script type="text/javascript">
        document.getElementById('userProfileForm').onsubmit = function(event) {
            event.preventDefault(); // Prevent the default form submission

            fetchPutUserProfile();
        };
        function searchUserEmailProfile() {
            let email = document.getElementById('email').value;
            email = email.trim();
            if (email.length > 0) {
                fetchGetUserProfile(email)
                    .then(user => loadUserCard(user))
                    .catch(error => clearUserCard());
            } else {
                clearUserCard();
            }
        }

        function loadUserCard(user) {
            console.log("2 ", user.name);
            //alert(user.name);
            let item = document.getElementById('name');
            item.value = user.name;
            item = document.getElementById('trade_id');
            item.value = user.trade_id;
            item = document.getElementById('role_type');
            item.value = user.role_type;
        }

        function clearUserCard() {
            let item = document.getElementById('name');
            item.value = "";
            item = document.getElementById('email');
            item.value = "";
            item = document.getElementById('trade_id');
            item.value = "";
            item = document.getElementById('role_type');
            item.value = "";
        }
        // fetch products by brand
        function fetchGetUserProfile(email) {
            const newpath = "{{ route('roleprofile.mail', ['email' => ':email']) }}".replace(':email', encodeURIComponent(email));
            //alert(newpath);
            return new Promise((resolve, reject) => {
                //fetch(`/isolatedprofile.mail/${email}`)
                fetch(`${newpath}`)
                    .then(response => response.json())
                    .then(user => {
                        //console.log("1 ",user);
                        resolve(user);
                    })
                    .catch(error => {
                        console.error('Error fetching user by email:', error);
                        reject(error); // Pass error to the calling function
                    });

            });
        }

        function fetchPutUserProfile() {
            const email= document.getElementById('email').value;
            const role_type = document.getElementById('role_type').value;
            console.log("Fetching user profile ",email, ' - ', role_type);
            const newpath = "{{ route('roleprofile.update', ['email' => ':email','role_type' => ':role_type' ]) }}"
                            .replace(':email', encodeURIComponent(email))
                            .replace(':role_type', role_type );
            //alert(newpath);
            return new Promise((resolve, reject) => {
                //fetch(`/isolatedprofile.mail/${email}`)
                fetch(`${newpath}`,
                    { method: 'PUT'
    //                 {
    // method: 'POST',
    // mode: 'cors',
    // cache: 'no-cache',
    // credentials: 'same-origin',
    // headers: {
    //   'Content-Type': 'application/json'

    // },
                    })

                    .then(response => response.json())
                    .then(user => {
                        //console.log("1 ",user);
                        resolve(user);
                    })
                    .catch(error => {
                        console.error('Error fetching user by email:', error);
                        reject(error); // Pass error to the calling function
                    });

            });
        }


    </script>

</x-app-layout>
