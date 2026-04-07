<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Edit Article') }}
            </h2>
            @if(in_array(auth()->user()->role, ['admin', 'moderator']))
                <a href="{{ route('admin.articles.index') }}" class="inline-flex items-center px-3 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-medium rounded-lg transition-colors">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    Back
                </a>
            @else
                <a href="{{ route('articles.index') }}" class="inline-flex items-center px-3 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-medium rounded-lg transition-colors">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    Back
                </a>
            @endif
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            @if($errors->any())
                <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                    <ul class="list-disc list-inside">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <form method="POST" action="{{ route('articles.update', $article->slug) }}" enctype="multipart/form-data" id="articleForm" target="_blank">
                        @csrf
                        @method('PUT')

                        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                            <!-- Main Content Area (Left Side - 2/3 width) -->
                            <div class="lg:col-span-2 space-y-6">
                                <!-- Title -->
                                <div>
                                    <label for="title" class="block text-sm font-medium text-gray-700 mb-2">Article Title *</label>
                                    <input type="text" name="title" id="title" value="{{ old('title', $article->title) }}" required
                                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                </div>

                                <!-- Content -->
                                <div>
                                    <label for="content" class="block text-sm font-medium text-gray-700 mb-2">Content *</label>
                                    <textarea name="content" id="content" rows="20">{{ old('content', $article->content) }}</textarea>
                                </div>

                                <!-- Summary -->
                                <div>
                                    <label for="summary" class="block text-sm font-medium text-gray-700 mb-2">Summary</label>
                                    <textarea name="summary" id="summary" rows="3"
                                        placeholder="Brief description of the article..."
                                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">{{ old('summary', $article->summary) }}</textarea>
                                </div>

                                <!-- Status -->
                                <div>
                                    <label for="status" class="block text-sm font-medium text-gray-700 mb-2">Status *</label>
                                    <select name="status" id="status" required
                                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                        <option value="draft" {{ old('status', $article->status) === 'draft' ? 'selected' : '' }}>Save as Draft</option>
                                        <option value="pending" {{ old('status', $article->status) === 'pending' ? 'selected' : '' }}>Submit for Review</option>
                                        @if($article->status === 'rejected')
                                            <option value="rejected" {{ old('status', $article->status) === 'rejected' ? 'selected' : '' }}>Rejected</option>
                                        @endif
                                        @if(in_array(auth()->user()->role, ['admin', 'moderator']) && $article->status === 'published')
                                            <option value="published" {{ old('status', $article->status) === 'published' ? 'selected' : '' }}>Published</option>
                                        @endif
                                    </select>
                                </div>

                                @php
                                    $matchedDraftPresetKey = '';
                                    foreach (config('article.draft_reason_presets', []) as $key => $text) {
                                        if (trim((string) ($article->draft_reason ?? '')) === $text) {
                                            $matchedDraftPresetKey = $key;
                                            break;
                                        }
                                    }
                                @endphp
                                @include('articles.partials.draft-reason-options', ['selectedKey' => old('draft_reason_preset', $matchedDraftPresetKey)])

                                <!-- Show Lock Icon Toggle -->
                                <div>
                                    <label class="flex items-center space-x-3">
                                        <input type="hidden" name="show_lock_icon" value="0">
                                        <input type="checkbox" name="show_lock_icon" value="1"
                                            {{ old('show_lock_icon', $article->show_lock_icon) ? 'checked' : '' }}
                                            class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                        <span class="text-sm font-medium text-gray-700">Show lock icon on article page</span>
                                    </label>
                                    <p class="text-xs text-gray-500 mt-1 ml-7">Enable this to display a lock icon in the top-right corner of the article.</p>
                                </div>

                                <!-- References -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">References</label>
                                    <div id="referencesFields" class="space-y-3">
                                        @php
                                            $references = old('references', $article->references ?? []);
                                            // Ensure references is an array
                                            if (is_string($references)) {
                                                $references = json_decode($references, true) ?? [];
                                            }
                                            if (!is_array($references)) {
                                                $references = [];
                                            }
                                        @endphp
                                        @if(is_array($references) && count($references) > 0)
                                            @foreach($references as $index => $reference)
                                                <div class="reference-field-group flex gap-2">
                                                    <input type="text" name="references[{{ $index }}][title]" placeholder="Reference Title"
                                                        value="{{ $reference['title'] ?? '' }}"
                                                        class="flex-1 rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                                    <input type="url" name="references[{{ $index }}][url]" placeholder="URL"
                                                        value="{{ $reference['url'] ?? '' }}"
                                                        class="flex-1 rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                                    <button type="button" onclick="this.parentElement.remove()" class="px-3 py-2 bg-red-500 text-white rounded hover:bg-red-600">×</button>
                                                </div>
                                            @endforeach
                                        @else
                                            <div class="reference-field-group flex gap-2">
                                                <input type="text" name="references[0][title]" placeholder="Reference Title"
                                                    class="flex-1 rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                                <input type="url" name="references[0][url]" placeholder="URL"
                                                    class="flex-1 rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                                <button type="button" onclick="this.parentElement.remove()" class="px-3 py-2 bg-red-500 text-white rounded hover:bg-red-600">×</button>
                                            </div>
                                        @endif
                                    </div>
                                    <button type="button" id="addReferenceField" class="mt-3 px-4 py-2 bg-gray-500 text-white rounded hover:bg-gray-600 text-sm">
                                        + Add Reference
                                    </button>
                                </div>
                            </div>

                            <!-- Infobox Sidebar (Right Side - 1/3 width) -->
                            <div class="lg:col-span-1 space-y-6">
                                <div class="bg-gray-50 border border-gray-200 rounded-lg p-4 sticky top-6">
                                    <h3 class="text-lg font-semibold text-gray-900 mb-4 pb-2 border-b border-gray-300">Article Infobox</h3>

                                    <!-- Infobox Image -->
                                    <div class="mb-4">
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Image</label>

                                        @if($article->infobox_image)
                                            <div class="mb-2">
                                                <img src="{{ Storage::url($article->infobox_image) }}" alt="Current image" class="w-full h-auto rounded-lg mb-2">
                                                <label class="flex items-center gap-2">
                                                    <input type="checkbox" name="remove_image" value="1" class="rounded">
                                                    <span class="text-sm text-red-600">Remove current image</span>
                                                </label>
                                            </div>
                                        @endif

                                        <!-- Custom Image Upload Area -->
                                        <div class="image-upload-wrapper">
                                            <input type="file" name="infobox_image" id="infobox_image" accept="image/*" class="hidden">

                                            <div id="imageUploadArea" class="border-2 border-dashed border-gray-300 rounded-lg p-4 text-center cursor-pointer hover:border-blue-500 transition-colors bg-white">
                                                <div id="imagePreviewContainer" class="hidden">
                                                    <img id="imagePreview" src="" alt="Preview" class="w-full h-auto rounded-lg mb-2">
                                                    <button type="button" id="removeImage" class="text-xs text-red-600 hover:text-red-800 font-medium">
                                                        × Remove Image
                                                    </button>
                                                </div>

                                                <div id="uploadPrompt">
                                                    <svg class="mx-auto h-12 w-12 text-gray-400 mb-2" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                                                        <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                                    </svg>
                                                    <p class="text-sm text-gray-600 mb-1">
                                                        <span class="font-semibold text-blue-600 hover:text-blue-700">Click to upload</span> or drag and drop
                                                    </p>
                                                    <p class="text-xs text-gray-500">PNG, JPG, GIF up to 2MB</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Info Fields (Key-Value Pairs) -->
                                    <div class="mb-4">
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Information Fields</label>
                                        <div id="infoFields" class="space-y-2">
                                            @php
                                                $infoFields = old('info') ?? $article->attributes->map(function($attr) {
                                                    return ['key' => $attr->key, 'value' => $attr->value];
                                                })->toArray();
                                            @endphp
                                            @if($infoFields && count($infoFields) > 0)
                                                @foreach($infoFields as $index => $info)
                                                    <div class="info-field-group">
                                                        <div class="flex gap-1 mb-2">
                                                            <input type="text" name="info[{{ $index }}][key]" placeholder="Key"
                                                                value="{{ $info['key'] ?? '' }}"
                                                                class="flex-1 text-sm rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                                            <button type="button" onclick="this.closest('.info-field-group').remove()" class="px-2 py-1 bg-red-500 text-white rounded hover:bg-red-600 text-sm">×</button>
                                                        </div>
                                                        <input type="text" name="info[{{ $index }}][value]" placeholder="Value"
                                                            value="{{ $info['value'] ?? '' }}"
                                                            class="w-full text-sm rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                                    </div>
                                                @endforeach
                                            @else
                                                <div class="info-field-group">
                                                    <div class="flex gap-1 mb-2">
                                                        <input type="text" name="info[0][key]" placeholder="Key (e.g., Born)"
                                                            class="flex-1 text-sm rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                                        <button type="button" onclick="this.closest('.info-field-group').remove()" class="px-2 py-1 bg-red-500 text-white rounded hover:bg-red-600 text-sm">×</button>
                                                    </div>
                                                    <input type="text" name="info[0][value]" placeholder="Value"
                                                        class="w-full text-sm rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                                </div>
                                            @endif
                                        </div>
                                        <button type="button" id="addInfoField" class="mt-3 w-full px-3 py-2 bg-blue-500 text-white rounded hover:bg-blue-600 text-sm">
                                            + Add Info Field
                                        </button>
                                    </div>

                                    <div class="text-xs text-gray-500 italic">
                                        <p>These fields will appear in the article's infobox, similar to Wikipedia articles.</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Submit Buttons -->
                        <div class="flex justify-end gap-4 pt-6 mt-6 border-t border-gray-200">
                            <a href="{{ in_array(auth()->user()->role, ['admin', 'moderator']) ? route('admin.articles.index') : route('articles.index') }}" class="px-6 py-2 bg-gray-300 text-gray-700 rounded hover:bg-gray-400">
                                Cancel
                            </a>
                            <button type="button" onclick="previewArticle()" class="px-6 py-2 bg-blue-600 text-white rounded hover:bg-purple-700 flex items-center gap-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>
                                Preview
                            </button>
                            <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                                Save Article
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script type="importmap">
    {
        "imports": {
            "ckeditor5": "https://cdn.ckeditor.com/ckeditor5/43.3.1/ckeditor5.js",
            "ckeditor5/": "https://cdn.ckeditor.com/ckeditor5/43.3.1/"
        }
    }
    </script>

    <script type="module">
        import {
            ClassicEditor,
            Essentials,
            Bold,
            Italic,
            Underline,
            Strikethrough,
            Font,
            Paragraph,
            Heading,
            List,
            Link,
            BlockQuote,
            Table,
            TableToolbar,
            MediaEmbed,
            Image,
            ImageToolbar,
            ImageCaption,
            ImageStyle,
            ImageUpload,
            FileRepository,
            HorizontalLine,
            Alignment
        } from 'ckeditor5';

        const csrfToken = '{{ csrf_token() }}';
        const uploadUrl = '{{ route("articles.upload-image") }}';

        class UploadAdapter {
            constructor(loader) {
                this.loader = loader;
            }

            upload() {
                return this.loader.file
                    .then(file => new Promise((resolve, reject) => {
                        const data = new FormData();
                        data.append('upload', file);
                        data.append('_token', csrfToken);

                        fetch(uploadUrl, {
                            method: 'POST',
                            body: data
                        })
                        .then(response => response.json())
                        .then(result => {
                            resolve({ default: result.url });
                        })
                        .catch(error => {
                            reject(error);
                        });
                    }));
            }

            abort() {}
        }

        function UploadAdapterPlugin(editor) {
            editor.plugins.get('FileRepository').createUploadAdapter = (loader) => {
                return new UploadAdapter(loader);
            };
        }

        let editor;
        window.editor = null; // Make editor globally accessible

        ClassicEditor
            .create(document.querySelector('#content'), {
                plugins: [
                    Essentials, Bold, Italic, Underline, Strikethrough, Font, Paragraph,
                    Heading, List, Link, BlockQuote, Table, TableToolbar, MediaEmbed,
                    Image, ImageToolbar, ImageCaption, ImageStyle, ImageUpload,
                    FileRepository, HorizontalLine, Alignment, UploadAdapterPlugin
                ],
                toolbar: {
                    items: [
                        'undo', 'redo', '|',
                        'heading', '|',
                        'fontSize', 'fontFamily', 'fontColor', 'fontBackgroundColor', '|',
                        'bold', 'italic', 'underline', 'strikethrough', '|',
                        'alignment', '|',
                        'link', 'uploadImage', 'mediaEmbed', 'blockQuote', 'horizontalLine', '|',
                        'bulletedList', 'numberedList', '|',
                        'insertTable'
                    ],
                    shouldNotGroupWhenFull: true
                },
                alignment: {
                    options: ['left', 'center', 'right', 'justify']
                },
                heading: {
                    options: [
                        { model: 'paragraph', title: 'Paragraph', class: 'ck-heading_paragraph' },
                        { model: 'heading1', view: 'h1', title: 'Heading 1', class: 'ck-heading_heading1' },
                        { model: 'heading2', view: 'h2', title: 'Heading 2', class: 'ck-heading_heading2' },
                        { model: 'heading3', view: 'h3', title: 'Heading 3', class: 'ck-heading_heading3' },
                        { model: 'heading4', view: 'h4', title: 'Heading 4', class: 'ck-heading_heading4' },
                        { model: 'heading5', view: 'h5', title: 'Heading 5', class: 'ck-heading_heading5' },
                        { model: 'heading6', view: 'h6', title: 'Heading 6', class: 'ck-heading_heading6' }
                    ]
                },
                table: {
                    contentToolbar: [
                        'tableColumn', 'tableRow', 'mergeTableCells'
                    ]
                },
                image: {
                    toolbar: [
                        'imageStyle:inline', 'imageStyle:block', 'imageStyle:side', '|',
                        'imageTextAlternative'
                    ]
                },
                extraPlugins: [UploadAdapterPlugin]
            })
            .then(newEditor => {
                editor = newEditor;
                window.editor = newEditor; // Make globally accessible
            })
            .catch(error => {
                console.error('CKEditor initialization error:', error);
            });

        // Form validation before submission
        document.getElementById('articleForm').addEventListener('submit', function(e) {
            if (!editor) {
                e.preventDefault();
                alert('Editor is not initialized yet. Please wait a moment.');
                return false;
            }

            const content = editor.getData().trim();
            if (!content) {
                e.preventDefault();
                alert('Please add content to your article.');
                return false;
            }
        });

        // Add Info Field
        document.getElementById('addInfoField').addEventListener('click', function() {
            const container = document.getElementById('infoFields');
            const index = container.children.length;
            const newField = document.createElement('div');
            newField.className = 'info-field-group';
            newField.innerHTML = `
                <div class="flex gap-1 mb-2">
                    <input type="text" name="info[${index}][key]" placeholder="Key (e.g., Born)"
                        class="flex-1 text-sm rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    <button type="button" onclick="this.closest('.info-field-group').remove()" class="px-2 py-1 bg-red-500 text-white rounded hover:bg-red-600 text-sm">×</button>
                </div>
                <input type="text" name="info[${index}][value]" placeholder="Value"
                    class="w-full text-sm rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
            `;
            container.appendChild(newField);
        });

        // Add Reference Field
        document.getElementById('addReferenceField').addEventListener('click', function() {
            const container = document.getElementById('referencesFields');
            const index = container.children.length;
            const newField = document.createElement('div');
            newField.className = 'reference-field-group flex gap-2';
            newField.innerHTML = `
                <input type="text" name="references[${index}][title]" placeholder="Reference Title"
                    class="flex-1 rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                <input type="url" name="references[${index}][url]" placeholder="URL"
                    class="flex-1 rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                <button type="button" onclick="this.parentElement.remove()" class="px-3 py-2 bg-red-500 text-white rounded hover:bg-red-600">×</button>
            `;
            container.appendChild(newField);
        });

        // Toggle draft notice options based on status (exclusive checkboxes → hidden draft_reason_preset)
        const statusSelect = document.getElementById('status');
        const draftReasonField = document.getElementById('draftReasonField');

        function toggleDraftReason() {
            if (!draftReasonField || !statusSelect) {
                return;
            }
            if (statusSelect.value === 'draft') {
                draftReasonField.style.display = 'block';
            } else {
                draftReasonField.style.display = 'none';
            }
        }

        function initDraftReasonCheckboxes() {
            const hidden = document.getElementById('draft_reason_preset');
            const boxes = document.querySelectorAll('.draft-reason-checkbox');
            if (!hidden || !boxes.length) {
                return;
            }

            function syncUiFromHidden() {
                const v = hidden.value;
                boxes.forEach(function(cb) {
                    cb.checked = (cb.getAttribute('data-draft-preset') === v);
                });
            }

            boxes.forEach(function(cb) {
                cb.addEventListener('change', function() {
                    if (cb.checked) {
                        hidden.value = cb.getAttribute('data-draft-preset');
                        boxes.forEach(function(other) {
                            if (other !== cb) {
                                other.checked = false;
                            }
                        });
                    } else if (hidden.value === cb.getAttribute('data-draft-preset')) {
                        hidden.value = '';
                    }
                });
            });

            syncUiFromHidden();
        }

        statusSelect.addEventListener('change', toggleDraftReason);
        toggleDraftReason();
        initDraftReasonCheckboxes();

        // Image Upload Functionality
        const imageInput = document.getElementById('infobox_image');
        const uploadArea = document.getElementById('imageUploadArea');
        const imagePreview = document.getElementById('imagePreview');
        const imagePreviewContainer = document.getElementById('imagePreviewContainer');
        const uploadPrompt = document.getElementById('uploadPrompt');
        const removeImageBtn = document.getElementById('removeImage');

        // Click to upload
        uploadArea.addEventListener('click', function(e) {
            if (e.target.id !== 'removeImage') {
                imageInput.click();
            }
        });

        // Handle file selection
        imageInput.addEventListener('change', function(e) {
            handleImageFile(e.target.files[0]);
        });

        // Drag and drop
        uploadArea.addEventListener('dragover', function(e) {
            e.preventDefault();
            uploadArea.classList.add('border-blue-500', 'bg-blue-50');
        });

        uploadArea.addEventListener('dragleave', function(e) {
            e.preventDefault();
            uploadArea.classList.remove('border-blue-500', 'bg-blue-50');
        });

        uploadArea.addEventListener('drop', function(e) {
            e.preventDefault();
            uploadArea.classList.remove('border-blue-500', 'bg-blue-50');

            const file = e.dataTransfer.files[0];
            if (file && file.type.startsWith('image/')) {
                // Set the file to the input
                const dataTransfer = new DataTransfer();
                dataTransfer.items.add(file);
                imageInput.files = dataTransfer.files;

                handleImageFile(file);
            }
        });

        // Remove image
        removeImageBtn.addEventListener('click', function(e) {
            e.stopPropagation();
            imageInput.value = '';
            imagePreview.src = '';
            imagePreviewContainer.classList.add('hidden');
            uploadPrompt.classList.remove('hidden');
        });

        function handleImageFile(file) {
            if (file && file.type.startsWith('image/')) {
                // Check file size (2MB)
                if (file.size > 2 * 1024 * 1024) {
                    alert('File size must be less than 2MB');
                    return;
                }

                const reader = new FileReader();
                reader.onload = function(e) {
                    imagePreview.src = e.target.result;
                    imagePreviewContainer.classList.remove('hidden');
                    uploadPrompt.classList.add('hidden');
                };
                reader.readAsDataURL(file);
            }
        }

        // Preview Article Function
        window.previewArticle = function() {
            if (!window.editor) {
                alert('Editor is still loading. Please wait a moment and try again.');
                return;
            }

            var title = document.querySelector('#title').value;
            var content = window.editor.getData();
            var summary = document.querySelector('#summary').value;

            if (!title) {
                alert('Please enter an article title');
                return;
            }

            if (!content) {
                alert('Please enter article content');
                return;
            }

            var infoFields = [];
            document.querySelectorAll('.info-field-group').forEach(function(group) {
                var key = group.querySelector('input[name*="[key]"]');
                var value = group.querySelector('input[name*="[value]"]');
                if (key && value && key.value && value.value) {
                    infoFields.push({ key: key.value, value: value.value });
                }
            });

            var references = [];
            document.querySelectorAll('.reference-field-group').forEach(function(group) {
                var refTitle = group.querySelector('input[name*="[title]"]');
                var url = group.querySelector('input[name*="[url]"]');
                if ((refTitle && refTitle.value) || (url && url.value)) {
                    references.push({
                        title: refTitle ? refTitle.value : '',
                        url: url ? url.value : ''
                    });
                }
            });

            // Create a form and submit to preview route
            var form = document.createElement('form');
            form.method = 'POST';
            form.action = '{{ route("articles.preview") }}';
            form.target = '_blank';

            // Add CSRF token
            var csrfInput = document.createElement('input');
            csrfInput.type = 'hidden';
            csrfInput.name = '_token';
            csrfInput.value = '{{ csrf_token() }}';
            form.appendChild(csrfInput);

            // Add title
            var titleInput = document.createElement('input');
            titleInput.type = 'hidden';
            titleInput.name = 'title';
            titleInput.value = title;
            form.appendChild(titleInput);

            // Add content
            var contentInput = document.createElement('input');
            contentInput.type = 'hidden';
            contentInput.name = 'content';
            contentInput.value = content;
            form.appendChild(contentInput);

            // Add summary
            if (summary) {
                var summaryInput = document.createElement('input');
                summaryInput.type = 'hidden';
                summaryInput.name = 'summary';
                summaryInput.value = summary;
                form.appendChild(summaryInput);
            }

            // Add draft_reason_preset for preview
            var draftPreset = document.querySelector('#draft_reason_preset')?.value;
            if (draftPreset) {
                var draftPresetInput = document.createElement('input');
                draftPresetInput.type = 'hidden';
                draftPresetInput.name = 'draft_reason_preset';
                draftPresetInput.value = draftPreset;
                form.appendChild(draftPresetInput);
            }

            // Add show_lock_icon
            var showLockIcon = document.querySelector('input[type="checkbox"][name="show_lock_icon"]');
            if (showLockIcon && showLockIcon.checked) {
                var lockIconInput = document.createElement('input');
                lockIconInput.type = 'hidden';
                lockIconInput.name = 'show_lock_icon';
                lockIconInput.value = '1';
                form.appendChild(lockIconInput);
            }

            // Status (draft / pending / rejected / published) for accurate preview
            var statusSelect = document.querySelector('#status');
            if (statusSelect && statusSelect.value) {
                var statusInput = document.createElement('input');
                statusInput.type = 'hidden';
                statusInput.name = 'status';
                statusInput.value = statusSelect.value;
                form.appendChild(statusInput);
            }

            // Rejection reasons + “show on article” checkboxes (same as main form)
            for (var ri = 0; ri < 4; ri++) {
                var reasonEl = document.querySelector('input[name="rejection_reason[' + ri + ']"]');
                if (reasonEl) {
                    var rIn = document.createElement('input');
                    rIn.type = 'hidden';
                    rIn.name = 'rejection_reason[' + ri + ']';
                    rIn.value = reasonEl.value || '';
                    form.appendChild(rIn);
                }
                var activeEl = document.querySelector('input[type="checkbox"][name="rejection_active[' + ri + ']"]');
                if (activeEl && activeEl.checked) {
                    var aIn = document.createElement('input');
                    aIn.type = 'hidden';
                    aIn.name = 'rejection_active[' + ri + ']';
                    aIn.value = '1';
                    form.appendChild(aIn);
                }
            }

            // Add info fields
            infoFields.forEach(function(field, index) {
                var keyInput = document.createElement('input');
                keyInput.type = 'hidden';
                keyInput.name = 'info[' + index + '][key]';
                keyInput.value = field.key;
                form.appendChild(keyInput);

                var valueInput = document.createElement('input');
                valueInput.type = 'hidden';
                valueInput.name = 'info[' + index + '][value]';
                valueInput.value = field.value;
                form.appendChild(valueInput);
            });

            // Add references as JSON
            var referencesInput = document.createElement('input');
            referencesInput.type = 'hidden';
            referencesInput.name = 'references';
            referencesInput.value = JSON.stringify(references);
            form.appendChild(referencesInput);

            // Add infobox image file if selected
            var infoboxImageInput = document.querySelector('input[name="infobox_image"]');
            if (infoboxImageInput && infoboxImageInput.files.length > 0) {
                // Clone the file input to include in the form
                var fileClone = infoboxImageInput.cloneNode(true);
                form.appendChild(fileClone);
                form.enctype = 'multipart/form-data';
            } else {
                // If no new file selected, send existing image path
                var existingImage = '{{ $article->infobox_image ?? "" }}';
                if (existingImage) {
                    var existingImageInput = document.createElement('input');
                    existingImageInput.type = 'hidden';
                    existingImageInput.name = 'existing_infobox_image';
                    existingImageInput.value = existingImage;
                    form.appendChild(existingImageInput);
                }
            }

            // Append form to body, submit, and remove
            document.body.appendChild(form);
            form.submit();
            document.body.removeChild(form);
        };

        function escapeHtml(text) {
            var div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }
    </script>

    <link rel="stylesheet" href="https://cdn.ckeditor.com/ckeditor5/43.3.1/ckeditor5.css" />

    <style>
        /* CKEditor Heading Styles */
        .ck-content h1 {
            font-size: 2em;
            font-weight: bold;
            margin: 0.67em 0;
        }
        .ck-content h2 {
            font-size: 1.5em;
            font-weight: bold;
            margin: 0.75em 0;
        }
        .ck-content h3 {
            font-size: 1.17em;
            font-weight: bold;
            margin: 0.83em 0;
        }
        .ck-content h4 {
            font-size: 1em;
            font-weight: bold;
            margin: 1em 0;
        }
        .ck-content h5 {
            font-size: 0.83em;
            font-weight: bold;
            margin: 1.17em 0;
        }
        .ck-content h6 {
            font-size: 0.67em;
            font-weight: bold;
            margin: 1.33em 0;
        }

        /* Ensure CKEditor has enough height */
        .ck-editor__editable {
            min-height: 500px;
        }

        /* Make infobox sticky on scroll */
        @media (min-width: 1024px) {
            .sticky {
                position: sticky;
                top: 1.5rem;
            }
        }
    </style>
</x-app-layout>
