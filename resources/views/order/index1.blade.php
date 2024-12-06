@extends('layouts.page')

@section('title', 'Brand Affinities')
@section('content')

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <div class="max-w-xl">
                    <section class="space-y-6">
                            <header>
                                <h2 class="text-lg font-medium text-gray-900">
                                    {{ __('Order Queries') }}
                                </h2>

                                <p class="mt-1 text-sm text-gray-600">
                                    {{ __("Select Filters") }}
                                </p>
                            </header>

                            <form id ="tradeForm" class="mt-6 space-y-6">
                                @csrf
                                @method('POST')
                                <input type="hidden"  id="sender_email" name="sender_email" value="{{ Auth::user()->email }}" />
                                <div class="mb-3">
                                    <x-input-label for="trade_name" :value="__('Trades')" />
                                    <select id="trade_name" name="trade_name" class="mt-1 block w-full border border-gray-100 rounded-md" >
                                      <option value=""> --- trade selector --- </option>
                                      @foreach ($trades as $key => $item) {
                                        <option value="{{ $item['name'] }}">{{ $item['name'] }}</option>
                                      @endforeach
                                    </select>
                                    <br />
                                    <x-secondary-button onclick="searchTrade()">{{ __('Search Trade') }}</x-primary-button>

                                </div>

                                <div>
                                    <x-input-label for="program" :value="__('Trade Program')" />
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
                                        <div class="filter-checkbox-label" for="is_program_active"> Trade Active
                                        </div>
                                    </div>
                                </div>


                                <div class="flex items-center gap-4">
                                    <x-primary-button>{{ __('Save') }}</x-primary-button>
                                </div>
                            </form>
                    </section>

                </div>
            </div>
        </div>
    </div>
    <div class="table-row" id="productContainer">

        @foreach ($trades as $trade)

                <div class="card">
                    <div class="card-body">

                        <h6 class="card-title">{{ $trade['program'] }}</h6>
                        <h6 class="card-title">{{ $trade['trade_name'] }}</h6>
                    </div>
                    <div class="card-body-text">
                        <p class="card-text">{{ $trade['program_url'] }}</p>
                        <p class="card-text"> Active : {{ $trade['is_program_active'] }}</p>
                    </div>
                </div>

        @endforeach
    </div>
    <script type="text/javascript">

    document.addEventListener('DOMContentLoaded', (event) => {
        const form = document.getElementById("tradeForm");

        // Correctly bind the submit event and prevent default behavior
        form.addEventListener('submit', (event) => {
            fetchPostTrade(event); // Pass the event to fetchPostTrade
        });

        const brands = document.getElementById("trade_name");
        brands.addEventListener('change', () => searchTrade());
    });


    function searchTrade() {
            let trade = document.getElementById('trade_name').value;
            trade = trade.trim();
            if (trade.length > 0) {
                fetchGetTrade(trade)
                    .then(item => loadTrade(item))
                    .catch(error => clearTrade());
            } else {
                clearTrade();
            }
        }

        function loadTrade(trade) {
            //console.log(trade);
            //console.log("2  name ", trade.program, " url ", trade.program_url);
            let item = document.getElementById('program');
            item.value = trade.program;
            item = document.getElementById('program_url');
            item.value = trade.program_url;
            item = document.getElementById('program_image');
            item.value = trade.program_image;
            item = document.getElementById('is_program_active');
            item.checked = (trade.is_program_active == 1) ? true : false;
            //item.value = trade.is_program_active;
        }

        function clearTrade() {
            let item = document.getElementById('program');
            item.value = "";
            // item = document.getElementById('trade_name');
            // item.value = "";
            item = document.getElementById('program_url');
            item.value = "";
            item = document.getElementById('program_image');
            item.value = "";
            item = document.getElementById('is_program_active');
            item.checked =  true;
            item.value = "1";
        }
        // fetch products by trade_name
        function fetchGetTrade(trade) {
            const newpath = "{{ route('trade.show', ['trade' => ':trade']) }}".replace(':trade', encodeURIComponent(trade));
            return new Promise((resolve, reject) => {
                //fetch(`/isolatedprofile.mail/${brand}`)
                fetch(`${newpath}`)
                    .then(response => response.json())
                    .then(affinities => {
                        //console.log(affinities);
                        resolve(trades[0]);
                    })
                    .catch(error => {
                        console.error('Error fetching trade by trade_name:', error);
                        reject(error); // Pass error to the calling function
                    });
            });
        }

        function fetchPostTrade(event) {
            event.preventDefault(); // Prevent the default form submission including GET method

            const sender_email = document.getElementById('sender_email').value;
            const brand = document.getElementById('trade_name').value;
            const program = document.getElementById('program').value;
            const image = document.getElementById('program_image').value;
            const is_active = document.getElementById('is_program_active').checked ? 1 : 0;
            const url = document.getElementById('program_url').value;

            const newpath = "{{ route('trade.save', ['brand' => ':brand' ]) }}"
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
                //alert('Trade saved successfully.');
                //refresh all affinities in productContainer elements
                location.reload();
            })
            .catch(error => {
                console.error('Error:', error);
                //alert('Failed to save trade.');
            });
        }


    </script>

@endsection
