<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Affinities') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <div class="max-w-xl">
                    <section class="space-y-6">
                            <header>
                                <h2 class="text-lg font-medium text-gray-900">
                                    {{ __('Affinity Record') }}
                                </h2>

                                <p class="mt-1 text-sm text-gray-600">
                                    {{ __("Update / Create Affinity link") }}
                                </p>
                            </header>

                            <form id ="affinityForm" class="mt-6 space-y-6">
                                @csrf
                                @method('POST')
                                <input type="hidden"  id="sender_email" name="sender_email" value="{{ Auth::user()->email }}" />
                                <div class="mb-3">
                                    <x-input-label for="brand_name" :value="__('Brand')" />
                                    <select id="brand_name" name="brand_name" class="mt-1 block w-full border border-gray-100 rounded-md" >
                                      <option value=""> --- brand selector --- </option>
                                      @foreach ($brands as $key => $item) {
                                        <option value="{{ $item['name'] }}">{{ $item['name'] }}</option>
                                      @endforeach
                                    </select>
                                    <br />
                                    <x-secondary-button onclick="searchAffinity()">{{ __('Search affinity') }}</x-primary-button>

                                </div>

                                <div>
                                    <x-input-label for="program" :value="__('Affinity Program')" />
                                    <x-text-input id="program" name="program" type="text" class="mt-1 block w-full" />

                                </div>

                                <div>
                                    <x-input-label for="program_url" :value="__('Url')" />
                                    <x-text-input id="program_url" name="program_url" type="text" class="mt-1 block w-full" />
                                    <x-input-error class="mt-2" :messages="$errors->get('program_url')" />
                                </div>

                                <div>
                                    <x-input-label for="program_image" :value="__('Image')" />
                                    <x-text-input id="program_image" name="program_image" type="text" class="mt-1 block w-full" />
                                    <x-input-error class="mt-2" :messages="$errors->get('program_image')" />
                                </div>

                                <div>
                                    {{-- <x-input-label for="is_program_active" :value="__('Active')" />
                                    <x-text-input id="is_program_active" name="is_program_active" type="text" class="mt-1 block w-full" /> --}}

                                    <div class="filter-checkbox">
                                        <div class="filter-checkbox-input">
                                            <input id="is_program_active" name="is_program_active"  type="checkbox" checked />

                                        </div>
                                        <div class="filter-checkbox-label" for="is_program_active"> Affinity Active
                                        </div>
                                    </div>
                                </div>


                                <div class="flex items-center gap-4">
                                    <x-primary-button>{{ __('Save') }}</x-primary-button>

                                    @if (session('status') === 'affinity-updated')
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
    <div class="table-row" id="productContainer">

        @foreach ($affinities as $affinity)

                <div class="card">
                    <div class="card-body">

                        <h6 class="card-title">{{ $affinity['program'] }}</h6>
                        <h6 class="card-title">{{ $affinity['brand_name'] }}</h6>
                    </div>
                    <div class="card-body-text">
                        <p class="card-text">{{ $affinity['program_url'] }}</p>
                        <p class="card-text"> Active : {{ $affinity['is_program_active'] }}</p>
                    </div>
                </div>

        @endforeach
    </div>
    <script type="text/javascript">

    document.addEventListener('DOMContentLoaded', (event) => {
        const form = document.getElementById("affinityForm");

        // Correctly bind the submit event and prevent default behavior
        form.addEventListener('submit', (event) => {
            fetchPostAffinity(event); // Pass the event to fetchPostAffinity
        });

        const brands = document.getElementById("brand_name");
        brands.addEventListener('change', () => searchAffinity());
    });


    function searchAffinity() {
            let brand = document.getElementById('brand_name').value;
            brand = brand.trim();
            if (brand.length > 0) {
                fetchGetAffinity(brand)
                    .then(affinity => loadAffinity(affinity))
                    .catch(error => clearAffinity());
            } else {
                clearAffinity();
            }
        }

        function loadAffinity(affinity) {
            //console.log(affinity);
            //console.log("2  name ", affinity.program, " url ", affinity.program_url);
            let item = document.getElementById('program');
            item.value = affinity.program;
            item = document.getElementById('program_url');
            item.value = affinity.program_url;
            item = document.getElementById('program_image');
            item.value = affinity.program_image;
            item = document.getElementById('is_program_active');
            item.checked = (affinity.is_program_active == 1) ? true : false;
            //item.value = affinity.is_program_active;
        }

        function clearAffinity() {
            let item = document.getElementById('program');
            item.value = "";
            // item = document.getElementById('brand_name');
            // item.value = "";
            item = document.getElementById('program_url');
            item.value = "";
            item = document.getElementById('program_image');
            item.value = "";
            item = document.getElementById('is_program_active');
            item.checked =  true;
            item.value = "1";
        }
        // fetch products by brand_name
        function fetchGetAffinity(brand) {
            const newpath = "{{ route('affinity.show', ['brand' => ':brand']) }}".replace(':brand', encodeURIComponent(brand));
            return new Promise((resolve, reject) => {
                //fetch(`/isolatedprofile.mail/${brand}`)
                fetch(`${newpath}`)
                    .then(response => response.json())
                    .then(affinities => {
                        //console.log(affinities);
                        resolve(affinities[0]);
                    })
                    .catch(error => {
                        console.error('Error fetching affinity by brand_name:', error);
                        reject(error); // Pass error to the calling function
                    });

            });
        }

        function fetchPostAffinity(event) {
            event.preventDefault(); // Prevent the default form submission including GET method

            const sender_email = document.getElementById('sender_email').value;
            const brand = document.getElementById('brand_name').value;
            const program = document.getElementById('program').value;
            const image = document.getElementById('program_image').value;
            const is_active = document.getElementById('is_program_active').checked ? 1 : 0;
            const url = document.getElementById('program_url').value;

            const newpath = "{{ route('affinity.save', ['brand' => ':brand' ]) }}"
                            .replace(':brand', encodeURIComponent(brand));

            console.log(sender_email, newpath);

            fetch(newpath, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                },
                mode: 'cors',
                cache: 'no-cache',
                credentials: 'same-origin',
                body: JSON.stringify({
                    sender_email: sender_email,
                    program: program,
                    program_image: image,
                    program_url: url,
                    is_program_active: is_active
                })
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(data => {
                console.log(data);
                //alert('Affinity saved successfully.');
                //refresh all affinities in productContainer elements
                location.reload();
            })
            .catch(error => {
                console.error('Error:', error);
                //alert('Failed to save affinity.');
            });
        }


    </script>

</x-app-layout>
