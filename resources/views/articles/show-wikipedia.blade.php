<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8" />
    <meta name="keywords" content="HTML, CSS, JavaScript" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>{{ $article->title }} - {{ config('app.name') }}</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <link rel="stylesheet" href="{{ asset('encyclopediadrafts/styles.css') }}" />

    <style>
        .inner-content .inner-main .desc {
            position: relative;
        }
        .status-badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: 600;
            margin-left: 10px;
        }
        .status-draft { background: #6c757d; color: white; }
        .status-pending { background: #ffc107; color: #000; }
        .status-published { background: #28a745; color: white; }
        .status-rejected { background: #dc3545; color: white; }

        .action-buttons {
            position: absolute;
            top: 10px;
            right: 10px;
        }
        .action-buttons .btn {
            margin-left: 5px;
        }

        /* Reference list styling */
        .reference-list {
            list-style: none;
            counter-reset: ref-counter;
            padding-left: 0;
            margin-top: 15px;
        }
        .reference-item {
            counter-increment: ref-counter;
            margin-bottom: 8px;
            padding-left: 30px;
            position: relative;
            line-height: 1.6;
        }
        .reference-item::before {
            content: counter(ref-counter);
            position: absolute;
            left: 0;
            top: 0;
            background-color: #f0f0f0;
            border: 1px solid #a2a9b1;
            border-radius: 3px;
            padding: 2px 6px;
            font-size: 0.85em;
            font-weight: 600;
            color: #202122;
        }
        .reference-item .anchor {
            color: #0645ad;
            text-decoration: none;
        }
        .reference-item .anchor:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <main>
        <section class="main-content">
            <!-- Sidebar -->
            <aside class="aside-main">
                <div class="side-bar-main">
                    <div class="logo-main">
                        <img src="{{ asset('encyclopediadrafts/public/logo.png') }}" class="img-fluid logo-thumb" alt="Logo" />
                    </div>

                    <div class="list-items-main">
                        <ul class="side-ul">
                <li class="side-li">
                  <a
                    href="https://www.wikipedia.org/"
                    target="_blank"
                    class="anchor"
                    >Main page</a
                  >
                </li>
                <li class="side-li">
                  <a
                    href="https://en.wikipedia.org/wiki/Wikipedia:Contents"
                    target="_blank"
                    class="anchor"
                    >Contents</a
                  >
                </li>
                <li class="side-li">
                  <a
                    href="https://en.wikipedia.org/wiki/Portal:Current_events"
                    target="_blank"
                    class="anchor"
                    >Current events</a
                  >
                </li>
                <li class="side-li">
                  <a
                    href="https://en.wikipedia.org/wiki/Special:Random"
                    target="_blank"
                    class="anchor"
                    >Random article</a
                  >
                </li>
                <li class="side-li">
                  <a
                    href="https://en.wikipedia.org/wiki/Wikipedia:About"
                    target="_blank"
                    class="anchor"
                    >About Wikipedia</a
                  >
                </li>
                <li class="side-li">
                  <a
                    href="https://en.wikipedia.org/wiki/Wikipedia:Contact_us"
                    target="_blank"
                    class="anchor"
                    >Contact us</a
                  >
                </li>
                <li class="side-li">
                  <a
                    href="https://donate.wikimedia.org/wiki/Special:FundraiserRedirector?utm_source=donate&utm_medium=sidebar&utm_campaign=C13_en.wikipedia.org&uselang=en"
                    target="_blank"
                    class="anchor"
                    >Donate</a
                  >
                </li>
              </ul>

              <h6 class="side-head">Contribute</h6>

              <ul class="side-ul">
                <li class="side-li">
                  <a
                    href="https://en.wikipedia.org/wiki/Help:Contents"
                    target="_blank"
                    class="anchor"
                    >Help</a
                  >
                </li>
                <li class="side-li">
                  <a
                    href="https://en.wikipedia.org/wiki/Help:Introduction"
                    target="_blank"
                    class="anchor"
                    >Learn to edit</a
                  >
                </li>
                <li class="side-li">
                  <a
                    href="https://en.wikipedia.org/wiki/Wikipedia:Community_portal"
                    target="_blank"
                    class="anchor"
                    >Community portal</a
                  >
                </li>
                <li class="side-li">
                  <a
                    href="https://en.wikipedia.org/wiki/Special:RecentChanges"
                    target="_blank"
                    class="anchor"
                    >Recent changes</a
                  >
                </li>
                <li class="side-li">
                  <a
                    href="https://en.wikipedia.org/wiki/Wikipedia:File_Upload_Wizard"
                    target="_blank"
                    class="anchor"
                    >Upload file</a
                  >
                </li>
              </ul>
                    </div>
                </div>
            </aside>

            <!-- Main Content -->
            <div class="inner-content">
                <div class="inner-main">
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-12">
                                <div class="desc">
                                    <h6 class="inner-head">
                                        {{ $article->title }}
                                        <span class="status-badge status-{{ $article->status }}">
                                            {{ ucfirst($article->status) }}
                                        </span>
                                    </h6>
                                    <span class="inner-sm">Created by {{ $article->creator->name ?? 'Unknown' }} on {{ $article->created_at->format('F d, Y') }}</span>

                                    <!-- Action Buttons -->
                                    {{-- @if(auth()->user()->id === $article->created_by || in_array(auth()->user()->role, ['admin', 'moderator']))
                                        <div class="action-buttons">
                                            @if(auth()->user()->id === $article->created_by)
                                                <a href="{{ route('articles.edit', $article->slug) }}" class="btn btn-sm btn-primary">Edit</a>
                                                <form method="POST" action="{{ route('articles.destroy', $article->slug) }}" style="display: inline;"
                                                      onsubmit="return confirm('Are you sure you want to delete this article?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                                                </form>
                                            @endif

                                            @if(in_array(auth()->user()->role, ['admin', 'moderator']) && $article->status === 'pending')
                                                <form method="POST" action="{{ route('articles.approve', $article->slug) }}" style="display: inline;">
                                                    @csrf
                                                    <button type="submit" class="btn btn-sm btn-success">Approve</button>
                                                </form>
                                            @endif
                                        </div>
                                    @endif --}}
                                </div>

                                <!-- Status Notifications -->
                                @if($article->status === 'draft')
                                    <div class="row justify-content-center align-items-center">
                                        <div class="col-10">
                                            <div class="wiki-items">
                                                <div class="wk-issue">
                                                    <p>This article may meet Wikipedia's criteria for speedy deletion as pure vandalism. This includes blatant and obvious misinformation, and redirects created during cleanup of page move vandalism.</p>
                                                    <p>This article is an orphan, as no other articles link to it. Please introduce links to this page from related articles</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @elseif($article->status === 'pending')
                                    <div class="row justify-content-center align-items-center">
                                        <div class="col-10">
                                            <div class="wiki-items">
                                                <div class="wk-issue">
                                                    <p>This article may meet Wikipedia's criteria for speedy deletion as pure vandalism. This includes blatant and obvious misinformation, and redirects created during cleanup of page move vandalism.</p>
                                                    <p>This article is an orphan, as no other articles link to it. Please introduce links to this page from related articles</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @elseif($article->status === 'rejected')
                                    <div class="row justify-content-center align-items-center">
                                        <div class="col-10">
                                            <div class="wiki-items">
                                                <div class="wk-issue">
                                                    <p>This article may meet Wikipedia's criteria for speedy deletion as pure vandalism. This includes blatant and obvious misinformation, and redirects created during cleanup of page move vandalism.</p>
                                                    <p>This article is an orphan, as no other articles link to it. Please introduce links to this page from related articles</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endif

                                <div class="row">
                                    <!-- Main Article Content -->
                                    <div class="col-12 col-xl-9">
                                        <div class="desc custom-table">
                                            @if($article->summary)
                                                <p><strong>{{ $article->summary }}</strong></p>
                                            @endif

                                            {!! $article->content !!}
                                        </div>
                                    </div>

                                    <!-- Infobox Sidebar -->
                                    @if($article->infobox_image || $article->attributes->count() > 0)
                                        <div class="col-12 col-xl-3">
                                            <div class="person-box">
                                                @if($article->infobox_image)
                                                    <div class="person-img">
                                                        <img src="{{ Storage::url($article->infobox_image) }}" class="img-fluid thumb" alt="{{ $article->title }}" />
                                                    </div>
                                                @endif

                                                @foreach($article->attributes as $attribute)
                                                    <ul class="person-desc">
                                                        <li class="per-li">
                                                            <h6 class="per-h">{{ $attribute->key }}</h6>
                                                        </li>
                                                        <li class="per-li">
                                                            <p class="per-p">{{ $attribute->value }}</p>
                                                        </li>
                                                    </ul>
                                                @endforeach
                                            </div>
                                        </div>
                                    @endif

                                    <!-- References Section -->
                                    @php
                                        // Debug and ensure references is an array
                                        $refs = $article->references;
                                        if (is_string($refs) && !empty($refs)) {
                                            $refs = json_decode($refs, true);
                                        }
                                        if (!is_array($refs)) {
                                            $refs = [];
                                        }
                                        // Filter out empty references
                                        $refs = array_filter($refs, function($ref) {
                                            return !empty($ref['title']) || !empty($ref['url']);
                                        });
                                    @endphp
                                    
                                    @if(count($refs) > 0)
                                        <div class="col-12">
                                            <div class="desc custom-table" style="margin-top: 30px;">
                                                <h2 style="font-size: 24px; font-weight: 400; border-bottom: 1px solid #a2a9b1; padding-bottom: 4px; margin-bottom: 15px;">References</h2>
                                                
                                                <!-- Wikipedia-style reference list with columns -->
                                                <div style="column-count: 2; column-gap: 30px; -webkit-column-count: 2; -moz-column-count: 2;">
                                                    <ol style="list-style-position: outside; padding-left: 0; margin-left: 20px; font-size: 13px; line-height: 1.8;">
                                                        @foreach($refs as $index => $reference)
                                                            <li style="break-inside: avoid-column; -webkit-column-break-inside: avoid; page-break-inside: avoid; margin-bottom: 10px; padding-right: 10px;">
                                                                @if(!empty($reference['title']) && !empty($reference['url']))
                                                                    <a href="{{ $reference['url'] }}" target="_blank" rel="nofollow noopener" style="color: #0645ad; text-decoration: none; word-wrap: break-word;">{{ $reference['title'] }}</a>
                                                                @elseif(!empty($reference['title']))
                                                                    <span style="word-wrap: break-word;">{{ $reference['title'] }}</span>
                                                                @elseif(!empty($reference['url']))
                                                                    <a href="{{ $reference['url'] }}" target="_blank" rel="nofollow noopener" style="color: #0645ad; text-decoration: none; word-wrap: break-word;">{{ $reference['url'] }}</a>
                                                                @endif
                                                            </li>
                                                        @endforeach
                                                    </ol>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Categories Section (Wikipedia Style) -->
                        <div class="row mt-4">
                            <div class="col-12">
                                <div class="categories-section" style="background-color: #f8f9fa; border: 1px solid #a2a9b1; padding: 12px 15px; margin-top: 20px;">
                                    <div style="color: #54595d; font-size: 13px; font-weight: 600; margin-bottom: 8px;">
                                        Categories:
                                    </div>
                                    <div class="category-links" style="display: flex; flex-wrap: wrap; gap: 5px; font-size: 13px;">
                                        <!-- Dynamic Categories Based on Article Metadata -->
                                        @php
                                            $categories = [];

                                            // Add status-based category
                                            if($article->status === 'published') {
                                                $categories[] = 'Published Articles';
                                            }

                                            // Add date-based category
                                            $categories[] = $article->created_at->format('Y') . ' articles';
                                            $categories[] = $article->created_at->format('F Y');

                                            // Add author category
                                            if($article->creator) {
                                                $categories[] = 'Articles by ' . $article->creator->name;
                                            }

                                            // You can add more dynamic categories based on article content or tags
                                            $categories[] = 'Encyclopedia entries';
                                        @endphp

                                        @foreach($categories as $index => $category)
                                            <span>
                                                <a href="javascript:;" style="color: #0645ad; text-decoration: none; padding: 0 3px;">{{ $category }}</a>
                                                @if($index < count($categories) - 1)
                                                    <span style="color: #54595d;">|</span>
                                                @endif
                                            </span>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Footer -->
                        <div class="row">
                            <div class="col-12">
                                <div class="bottom-div">
                                    <div class="row align-items-center">
                                        <div class="col-md-3">
                                            <div class="bottom-logo">
                                                <img src="{{ asset('encyclopediadrafts/public/wiki.jpeg') }}" class="img-fluid thumb" alt="Wiki" />
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="bottom-desc">
                                                <h6 class="btm-head">{{ config('app.name') }}</h6>
                                                <p class="para">Powered by <a href="javascript:;" class="name">Encyclopedia Drafts</a></p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="container-fluid">
                    <div class="row">
                        <div class="col-12">
                            <p class="copyright">© Copyright {{ date('Y') }} - {{ config('app.name') }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script>
        $(document).ready(function () {
            $(".custom-table").find("table").addClass("table table-bordered");
        });
    </script>
</body>
</html>
