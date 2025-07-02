@include('public.layout.header')

@component('public.layout.breadcrumb', ['breadcrumb' => ['Home' => '/']])
    @slot('active') {{ $page_detail->page_slug }} @endslot
@endcomponent

@component('public.partials.page-header')
    @slot('title') {{ $page_detail->page_title }} @endslot
@endcomponent

<div id="site-content">
    <div class="container py-5">
        {{-- Description from database --}}
        <div class="row mb-4">
            <div class="col-lg-10 mx-auto">
                {!! htmlspecialchars_decode($page_detail->description) !!}
            </div>
        </div>

        {{-- AI Chat UI --}}
        <div class="row">
            <div class="col-lg-10 mx-auto">
                <hr>
                <h4 class="mb-4">💬 Travel Assistant Chat</h4>

                <div id="chatbox" class="border rounded shadow-sm p-3 mb-4"
                     style="height: 400px; overflow-y: auto; background: #ffffff;">
                </div>

                <div class="input-group mb-5">
                    <input type="text" id="userMessage" class="form-control border-success" placeholder="Ask your travel question...">
                    <button onclick="sendMessage()" class="btn btn-success px-4">Send</button>
                </div>
            </div>
        </div>
    </div>
</div>

@include('public.layout.footer')

@push('header_script')
<style>
    #chatbox div.user { text-align: right; margin: 8px 0; }
    #chatbox div.bot { text-align: left; margin: 8px 0; }
    .chat-bubble {
        display: inline-block;
        padding: 10px 15px;
        border-radius: 20px;
        max-width: 80%;
        word-wrap: break-word;
    }
    .user .chat-bubble { background-color: #d4edda; color: #155724; }
    .bot .chat-bubble { background-color: #e2e3e5; color: #333; }
</style>
@endpush

@push('footer_script')
<script>
    
</script>
@endpush
