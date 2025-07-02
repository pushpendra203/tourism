<footer id="footer">
    @php $siteInfo = site_settings(); @endphp
    <div class="container-xl container-fluid">
        <div class="row">
            <div class="col-lg-6 col-md-6 col-sm-6 mb-4">
                <div class="footer-widget">
                    <h5 class="logo text-uppercase mb-4">
                        <a href="{{ url('/') }}">{{ $siteInfo->com_name }}</a>
                    </h5>
                    <p class="mb-4">{{ $siteInfo->description }}</p>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 col-sm-6 mb-4">
                <div class="footer-widget">
                    <h6 class="text-uppercase mb-4">Plans</h6>
                    <ul class="newsfeed text-capitalize">
                        @php $plan = plan_pages(); @endphp
                        @if ($plan->isNotEmpty())
                            @foreach ($plan as $item)
                                <li><a href="{{ url('/plan/' . $item->title_slug) }}">{{ $item->title_slug }}</a></li>
                            @endforeach
                        @endif
                    </ul>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 col-sm-6 mb-4">
                <div class="footer-widget">
                    <h6 class="text-uppercase mb-4">Pages</h6>
                    <ul class="newsfeed text-capitalize">
                        @php $pages = site_pages(); @endphp
                        @if ($pages->isNotEmpty())
                            @foreach ($pages as $page)
                                @if ($page->show_in_footer == '1')
                                    <li><a href="{{ url($page->page_slug) }}">{{ $page->page_title }}</a></li>
                                @endif
                            @endforeach
                        @endif
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <div class="footer-widget footer-section">
        <div class="container">
            <div class="row">
                <div class="col-lg-6 mb-lg-0 col-md-6 mb-md-0 mb-2">
                    <p class="mb-0">{{ $siteInfo->footer_copyright }}</p>
                </div>
            </div>
        </div>
    </div>
</footer>
</div>

{{-- Scripts --}}
<script src="{{ asset('public/assets/js/jquery.min.js') }}"></script>
<script src="{{ asset('public/assets/public/js/popper.min.js') }}"></script>
<script src="{{ asset('public/assets/public/js/bootstrap5.0.2.min.js') }}"></script>
<script src="{{ asset('public/assets/public/js/owl.carousel.min.js') }}"></script>
<script src="{{ asset('public/assets/public/js/jquery.flexslider-min.js') }}"></script>
<script src="{{ asset('public/assets/js/jquery.validate.min.js') }}"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/Swiper/9.2.4/swiper-bundle.min.js"></script>
<script src="https://checkout.razorpay.com/v1/checkout.js"></script>
<script src="{{ asset('public/assets/js/action.js') }}"></script>

<input type="hidden" class="site-url" value="{{ url('/') }}">

<script>
    $(document).ready(function() {
        $('.flexslider').flexslider({
            animation: "slide",
            controlNav: "thumbnails"
        });

        $('.all-category').click(function() {
            $(this).parent('li').siblings().children('.category_name').prop('checked', false);
            $('#filter-form').submit();
        });

        $('.all-location').click(function() {
            $(this).parent('li').siblings().children('.location_name').prop('checked', false);
            $('#filter-form').submit();
        });

        $('.trending-carousel').owlCarousel({
            loop: false,
            margin: 15,
            nav: true,
            responsive: {
                0: {
                    items: 1
                },
                600: {
                    items: 2
                },
                1000: {
                    items: 4
                }
            }
        });

        $('.related-carousel').owlCarousel({
            loop: false,
            margin: 30,
            nav: false,
            responsive: {
                0: {
                    items: 1
                },
                450: {
                    items: 2
                },
                800: {
                    items: 2
                },
                1000: {
                    items: 3
                }
            }
        });
    });

 async function sendMessage() {
        const input = document.getElementById('userMessage');
        const chatbox = document.getElementById('chatbox');
        const message = input.value.trim();

        if (!message) return;

        chatbox.innerHTML += `<div class="user"><span class="chat-bubble">${message}</span></div>`;
        input.value = '';

        try {
            const response = await fetch("{{ url('/ask-gemini') }}", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": "{{ csrf_token() }}"
                },
                body: JSON.stringify({ message })
            });

            const data = await response.json();
            chatbox.innerHTML += `<div class="bot"><span class="chat-bubble">${data.reply}</span></div>`;
            chatbox.scrollTop = chatbox.scrollHeight;
        } catch (error) {
            chatbox.innerHTML += `<div class="bot"><span class="chat-bubble">Something went wrong.</span></div>`;
        }
    }
    




</script>



</body>

</html>
