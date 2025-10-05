<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Welcome to Rhymes Platform') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="text-center py-12">
                        <svg class="mx-auto h-16 w-16 text-blue-500 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                        </svg>
                        
                        <h3 class="text-2xl font-bold text-gray-900 mb-4">Welcome to Rhymes Author Platform</h3>
                        <p class="text-lg text-gray-600 mb-8 max-w-2xl mx-auto">
                            Submit your books for stocking consideration at Rovingheights. Track reviews, monitor sales, and manage your royalties all in one place.
                        </p>

                        <div class="bg-blue-50 border border-blue-200 rounded-lg p-6 mb-8 max-w-3xl mx-auto">
                            <h4 class="text-lg font-semibold text-blue-900 mb-3">Get Started</h4>
                            <div class="text-left space-y-3 text-blue-800">
                                <div class="flex items-start">
                                    <span class="flex-shrink-0 w-6 h-6 bg-blue-600 text-white rounded-full flex items-center justify-center text-sm font-bold mr-3 mt-0.5">1</span>
                                    <div>
                                        <strong>Submit Your First Book</strong>
                                        <p class="text-blue-700">Upload your book details including ISBN, title, genre, and description for review by our team.</p>
                                    </div>
                                </div>
                                <div class="flex items-start">
                                    <span class="flex-shrink-0 w-6 h-6 bg-blue-600 text-white rounded-full flex items-center justify-center text-sm font-bold mr-3 mt-0.5">2</span>
                                    <div>
                                        <strong>Get Approved</strong>
                                        <p class="text-blue-700">Our team reviews submissions within 5-7 business days. Once approved, you become an Author.</p>
                                    </div>
                                </div>
                                <div class="flex items-start">
                                    <span class="flex-shrink-0 w-6 h-6 bg-blue-600 text-white rounded-full flex items-center justify-center text-sm font-bold mr-3 mt-0.5">3</span>
                                    <div>
                                        <strong>Track & Earn</strong>
                                        <p class="text-blue-700">Monitor sales in real-time, view earnings, and request payouts through your author dashboard.</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <a href="{{ route('author.books.create') }}" class="inline-flex items-center px-6 py-3 bg-blue-600 border border-transparent rounded-md font-semibold text-sm text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            Submit Your First Book
                        </a>
                    </div>
                </div>
            </div>

            <!-- Features Overview -->
            <div class="mt-8 grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-center">
                        <svg class="mx-auto h-12 w-12 text-green-500 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <h3 class="text-lg font-semibold text-gray-900 mb-2">Easy Submission</h3>
                        <p class="text-gray-600">Simple form to submit your book details for review by Rovingheights team.</p>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-center">
                        <svg class="mx-auto h-12 w-12 text-blue-500 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                        </svg>
                        <h3 class="text-lg font-semibold text-gray-900 mb-2">Real-time Tracking</h3>
                        <p class="text-gray-600">Monitor your book sales and earnings with live updates from our ERP system.</p>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-center">
                        <svg class="mx-auto h-12 w-12 text-purple-500 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                        <h3 class="text-lg font-semibold text-gray-900 mb-2">Secure Payouts</h3>
                        <p class="text-gray-600">Request payouts of your royalties with admin approval and secure processing.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
