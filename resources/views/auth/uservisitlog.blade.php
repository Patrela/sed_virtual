@extends('layouts.page')

@section('title', 'Visits')
@section('content')

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <div class="max-w-xl">
                    <section class="space-y-6">
                        <header>
                            <h2 class="text-lg font-medium text-gray-900">
                                {{ $title }}
                            </h2>

                            <p class="mt-1 text-sm text-gray-600">
                                {{ __('Users or Trades Orders') }}
                            </p>
                        </header>

                        <form id ="visitForm" class="mt-6 space-y-6">
                            @csrf
                            @method('GET')
                            <input type="hidden" id="sender_email" name="sender_email" value="{{ Auth::user()->email }}" />
                            <div class="mb-3">
                                <x-input-label for="email" :value="__('User Email')" />
                                <x-text-input id="email" name="email" type="email" class="mt-1 block w-full"
                                    autocomplete="username" />
                            </div>
                            <div class="mb-3">
                                <x-input-label for="nit" :value="__('Trade Nit')" />
                                <x-text-input id="nit" name="nit" type="text" class="mt-1 block w-full" />
                            </div>
                            <div>
                                <x-input-label for="startdate" :value="__('Start')" />
                                <x-text-input id="startdate" name="startdate" type="date" class="mt-1 block w-full"
                                    :value="2024 - 01 - 01" min="2024-01-01" max="2040-12-31" />
                            </div>
                            <div>
                                <x-input-label for="enddate" :value="__('End')" />
                                <x-text-input id="enddate" name="enddate" type="date" class="mt-1 block w-full"
                                    :value="2040 - 12 - 31" min="2024-01-01" max="2040-12-31" />
                            </div>

                            <div class="flex items-center gap-4">
                                <x-primary-button>{{ __('Search') }}</x-primary-button>
                                {{-- <x-secondary-button onclick="searchVisits()">{{ __('Search') }}
                                    </x-primary-button> --}}
                            </div>
                        </form>
                    </section>

                </div>
            </div>
        </div>
    </div>

    <div class="table-row" id="order-container">
        <div class="modal-card-item-division">
            <div class="list-text-large"><strong>Name</strong>
            </div>
            <div class="list-text-title">Date
            </div>
            <div class="list-text-title">Visits
            </div>
        </div>
        @foreach ($visits as $key => $visit)
            <div class="modal-card-item-division">
                <div class="list-text-large">{{ $visit['name'] }}
                </div>
                <div class="list-text">{{ $visit['log_date'] }}
                </div>
                <div class="list-text">{{ $visit['entries'] }}
                </div>
            </div>
        @endforeach
    </div>
    <script type="text/javascript">
        document.addEventListener('DOMContentLoaded', (event) => {
            const form = document.getElementById("visitForm");

            // Correctly bind the submit event and prevent default behavior
            form.addEventListener('submit', (event) => {
                //searchVisits(event); // Pass the event to fetchPostAffinity
                //alert("Search form submitted");
                fetchgetVisitItem(event); // Pass the event to fetch
            });

        });

        function searchVisits() {
            let seek = document.getElementById('email').value;
            email = seek.trim();
            seek = document.getElementById('nit').value;
            nit = seek.trim();
            seekfield = (nit.length > 0) ? nit : email;
            if (seekfield.length > 0) {
                console.log(seekfield);
                alert(seekfield);
                startdate = document.getElementById('startdate').value; //textContent
                enddate = document.getElementById('enddate').value; //textContent
                fetchGetVisits(seekfield, startdate, enddate);
                // fetchGetVisits(email, document.getElementById('startdate').value, document.getElementById('enddate').value)
                //     .then(visits => loadVisitData(visits))
                //     .catch(error => clearVisitData());
            } else {
                clearVisitData();
            }
        }

        function fetchGetVisits(seekfield, startdate, enddate) {

            if (seekfield.indexOf("@") !== -1) {

                const urlpath = "{{ route('visits.show', ['item' => ':item']) }}".replace(":item", seekfield);
                console.log(encodeURIComponent(email));
                console.log(urlpath);
                console.log('start date', startdate, 'end date', enddate);

                fetch(urlpath, {
                        method: "GET",
                        headers: {
                            "Content-Type": "application/json",
                            "x-api-start": startdate,
                            "x-api-end": enddate,
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                                'content'), //
                        },
                        mode: 'cors', //
                        cache: 'no-cache', //
                        credentials: 'same-origin', //
                    })
                    .then((response) => response.json())
                    .then((data) => {
                        //resolve(data);
                        //if (data["code"] === 200) alert("correo enviado");
                        //if (data["code"] === 404) alert(data["message"]);
                        alert("works!");
                        console.log(data);
                    })
                    .catch((error) => {
                        // Check for 404 response specifically
                        if (error.response && error.response.status === 404) {
                            alert("Visits not found.");
                            console.error("Visits not found:", error.response.data); // Log error details for debugging
                        } else {
                            console.error("Error fetching visits:", error); // Log other errors
                        }
                    });
            }
        }

        function clearVisitData() {
            let item = document.getElementById('email');
            item.value = "";
            item = document.getElementById('nit');
            item.value = "";
            item = document.getElementById('startdate');
            item.value = "2024-01-01";
            item = document.getElementById('enddate');
            item.value = "2030-12-01";
            // item = document.getElementById('save_message');
            // item.innerText = "";

        }

        function fetchgetVisitItem(event) {
            event.preventDefault(); // Prevent the default form submission including GET method

            const email = document.getElementById('email').value;
            const nit = document.getElementById('nit').value;
            const startdate = document.getElementById('startdate').value;
            const enddate = document.getElementById('enddate').value;
            let item = (email.includes("@")) ? email : nit;
            if (item.length > 0) {
                const newpath = "{{ route('visits.show', ['item' => ':item']) }}".replace(':item', encodeURIComponent(
                    item));

                fetch(newpath, {
                        method: 'GET',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                            "x-api-start": startdate,
                            "x-api-end": enddate,
                        },
                        mode: 'cors',
                        cache: 'no-cache',
                        credentials: 'same-origin'
                    })
                    .then(response => {
                        if (!response.ok) {
                            throw new Error('Network response was not ok');
                        }
                        return response.text();
                        //return response.json();
                    })
                    .then(html => {
                        document.open();
                        document.write(html);
                        document.close();
                    })
                    // .then(data => {
                    //     console.log(data);
                    //     //alert('Affinity saved successfully.');
                    //     //refresh all affinities in productContainer elements
                    //     location.reload();
                    // })
                    .catch(error => {
                        console.error('Error:', error);
                        //alert('Failed to save affinity.');
                    });

            }
            else {
                alert("Select User email or Trade's nit");
            }
        }
    </script>
@endsection
