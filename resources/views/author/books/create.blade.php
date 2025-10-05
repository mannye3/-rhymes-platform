<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Submit New Book') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form method="POST" action="{{ route('author.books.store') }}">
                        @csrf

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- ISBN -->
                            <div>
                                <x-input-label for="isbn" :value="__('ISBN')" />
                                <x-text-input id="isbn" class="block mt-1 w-full" type="text" name="isbn" :value="old('isbn')" required />
                                <x-input-error :messages="$errors->get('isbn')" class="mt-2" />
                                <p class="text-sm text-gray-600 mt-1">Enter the 13-digit ISBN of your book</p>
                            </div>

                            <!-- Title -->
                            <div>
                                <x-input-label for="title" :value="__('Book Title')" />
                                <x-text-input id="title" class="block mt-1 w-full" type="text" name="title" :value="old('title')" required />
                                <x-input-error :messages="$errors->get('title')" class="mt-2" />
                            </div>

                            <!-- Genre -->
                            <div>
                                <x-input-label for="genre" :value="__('Genre')" />
                                <select id="genre" name="genre" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                                    <option value="">Select Genre</option>
                                    <option value="Fiction" {{ old('genre') == 'Fiction' ? 'selected' : '' }}>Fiction</option>
                                    <option value="Non-Fiction" {{ old('genre') == 'Non-Fiction' ? 'selected' : '' }}>Non-Fiction</option>
                                    <option value="Romance" {{ old('genre') == 'Romance' ? 'selected' : '' }}>Romance</option>
                                    <option value="Mystery" {{ old('genre') == 'Mystery' ? 'selected' : '' }}>Mystery</option>
                                    <option value="Thriller" {{ old('genre') == 'Thriller' ? 'selected' : '' }}>Thriller</option>
                                    <option value="Science Fiction" {{ old('genre') == 'Science Fiction' ? 'selected' : '' }}>Science Fiction</option>
                                    <option value="Fantasy" {{ old('genre') == 'Fantasy' ? 'selected' : '' }}>Fantasy</option>
                                    <option value="Biography" {{ old('genre') == 'Biography' ? 'selected' : '' }}>Biography</option>
                                    <option value="Self-Help" {{ old('genre') == 'Self-Help' ? 'selected' : '' }}>Self-Help</option>
                                    <option value="Business" {{ old('genre') == 'Business' ? 'selected' : '' }}>Business</option>
                                    <option value="Other" {{ old('genre') == 'Other' ? 'selected' : '' }}>Other</option>
                                </select>
                                <x-input-error :messages="$errors->get('genre')" class="mt-2" />
                            </div>

                            <!-- Price -->
                            <div>
                                <x-input-label for="price" :value="__('Price ($)')" />
                                <x-text-input id="price" class="block mt-1 w-full" type="number" name="price" :value="old('price')" step="0.01" min="0" required />
                                <x-input-error :messages="$errors->get('price')" class="mt-2" />
                            </div>

                            <!-- Book Type -->
                            <div class="md:col-span-2">
                                <x-input-label for="book_type" :value="__('Book Type')" />
                                <div class="mt-2 space-y-2">
                                    <label class="inline-flex items-center">
                                        <input type="radio" name="book_type" value="physical" {{ old('book_type') == 'physical' ? 'checked' : '' }} class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500">
                                        <span class="ml-2">Physical Book Only</span>
                                    </label>
                                    <label class="inline-flex items-center">
                                        <input type="radio" name="book_type" value="digital" {{ old('book_type') == 'digital' ? 'checked' : '' }} class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500">
                                        <span class="ml-2">Digital Book Only</span>
                                    </label>
                                    <label class="inline-flex items-center">
                                        <input type="radio" name="book_type" value="both" {{ old('book_type') == 'both' ? 'checked' : '' }} class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500">
                                        <span class="ml-2">Both Physical and Digital</span>
                                    </label>
                                </div>
                                <x-input-error :messages="$errors->get('book_type')" class="mt-2" />
                            </div>
                        </div>

                        <!-- Description -->
                        <div class="mt-6">
                            <x-input-label for="description" :value="__('Book Description')" />
                            <textarea id="description" name="description" rows="6" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>{{ old('description') }}</textarea>
                            <x-input-error :messages="$errors->get('description')" class="mt-2" />
                            <p class="text-sm text-gray-600 mt-1">Provide a detailed description of your book, including plot summary, target audience, and key themes.</p>
                        </div>

                        <div class="flex items-center justify-end mt-6 space-x-4">
                            <a href="{{ route('author.books.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                Cancel
                            </a>
                            <x-primary-button>
                                {{ __('Submit Book for Review') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Information Panel -->
            <div class="mt-6 bg-blue-50 border border-blue-200 rounded-lg p-6">
                <h3 class="text-lg font-semibold text-blue-900 mb-3">Submission Guidelines</h3>
                <ul class="text-blue-800 space-y-2">
                    <li>• Ensure your book has a valid ISBN-13</li>
                    <li>• Provide accurate pricing information</li>
                    <li>• Write a compelling description that highlights your book's unique value</li>
                    <li>• Once submitted, our team will review your book within 5-7 business days</li>
                    <li>• You'll receive email notifications about status changes</li>
                    <li>• Accepted books will be integrated with our inventory system</li>
                </ul>
            </div>
        </div>
    </div>
</x-app-layout>
