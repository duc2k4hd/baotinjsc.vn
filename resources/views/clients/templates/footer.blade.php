<!-- Footer -->
<footer class="footer">
    <div class="container">
        <div class="footer-grid">
            <div class="footer-col">
                <img src="{{ asset('clients/assets/img/business/' . ($settings->site_logo ?? 'logo.webp')) }}" 
                     alt="{{ $settings->site_name ?? 'Bảo Tín JSC' }}" class="footer-logo">
                <p>{{ $settings->site_description ?? 'Nhà cung cấp thiết bị tự động hóa công nghiệp hàng đầu Việt Nam' }}</p>
                @if($settings->site_tax_code ?? null)
                    <p><strong>MST:</strong> {{ $settings->site_tax_code }}</p>
                @endif
                @if($settings->contact_email ?? null)
                    <p><strong>Email:</strong> {{ $settings->contact_email }}</p>
                @endif
                <div class="social-icons">
                    @if($settings->facebook_link ?? null)
                        <a href="{{ $settings->facebook_link }}" target="_blank"><i class="fab fa-facebook"></i></a>
                    @endif
                    @if($settings->twitter_link ?? null)
                        <a href="{{ $settings->twitter_link }}" target="_blank"><i class="fab fa-twitter"></i></a>
                    @endif
                    @if($settings->instagram_link ?? null)
                        <a href="{{ $settings->instagram_link }}" target="_blank"><i class="fab fa-instagram"></i></a>
                    @endif
                </div>
            </div>
            <div class="footer-col">
                <h3>THÔNG TIN</h3>
                <ul>
                    <li><a href="{{ route('client.introduction.index') }}">Giới thiệu</a></li>
                    <li><a href="{{ route('client.contact.index') }}">Liên hệ</a></li>
                    <li><a href="{{ route('client.policy.privacy') }}">Chính sách bảo mật</a></li>
                    <li><a href="{{ route('client.policy.terms') }}">Điều khoản & Điều kiện</a></li>
                    <li><a href="{{ route('client.policy.delivery') }}">Chính sách giao hàng</a></li>
                    <li><a href="{{ route('client.policy.payment') }}">Chính sách thanh toán</a></li>
                    <li><a href="{{ route('client.policy.return') }}">Chính sách đổi trả</a></li>
                    <li><a href="{{ route('client.policy.warranty') }}">Chính sách bảo hành</a></li>
                    <li><a href="{{ route('client.policy.sale') }}">Chính sách bán hàng</a></li>
                </ul>
            </div>
            <div class="footer-col">
                <h3>NHẬN BẢN TIN</h3>
                <p>Nhận tin sản phẩm khuyến mãi và các sản phẩm mới nhất</p>
                <form class="newsletter-form" action="{{ route('client.newsletter.subscription') }}" method="POST" id="footer-newsletter-form">
                    @csrf
                    <input type="email" name="autosensor_main_newsletter_email" id="footer-newsletter-email" placeholder="Nhập email của bạn..." required>
                    <button type="submit">Gửi đi</button>
                </form>
                <div id="footer-newsletter-message" style="margin-top: 10px; display: none;"></div>
                @if(session('success'))
                    <div class="newsletter-message newsletter-success" style="margin-top: 10px; padding: 8px; background: #d4edda; color: #155724; border-radius: 4px; font-size: 14px;">
                        {{ session('success') }}
                    </div>
                @endif
                @if(session('error'))
                    <div class="newsletter-message newsletter-error" style="margin-top: 10px; padding: 8px; background: #f8d7da; color: #721c24; border-radius: 4px; font-size: 14px;">
                        {{ session('error') }}
                    </div>
                @endif
                @if($errors->has('email') || $errors->has('autosensor_main_newsletter_email'))
                    <div class="newsletter-message newsletter-error" style="margin-top: 10px; padding: 8px; background: #f8d7da; color: #721c24; border-radius: 4px; font-size: 14px;">
                        {{ $errors->first('email') ?: $errors->first('autosensor_main_newsletter_email') }}
                    </div>
                @endif
            </div>
            <div class="footer-col">
                @if(isset($supportStaff) && $supportStaff->count() > 0)
                    <h3>CHĂM SÓC KHÁCH HÀNG</h3>
                    <ul class="footer-support-staff">
                        @foreach($supportStaff as $staff)
                            <li class="footer-support-item">
                                <div class="footer-support-info">
                                    <span class="footer-support-name">{{ $staff->name }}</span>
                                    @if($staff->role)
                                        <span class="footer-support-role">{{ $staff->role }}</span>
                                    @endif
                                </div>
                                <div class="footer-support-actions">
                                    @if($staff->phone)
                                        <a href="tel:{{ $staff->phone }}" class="footer-support-link footer-support-phone" title="Gọi điện: {{ $staff->phone }}">
                                            <i class="fas fa-phone"></i>
                                        </a>
                                    @endif
                                    @if($staff->zalo)
                                        <a href="https://zalo.me/{{ $staff->zalo }}" target="_blank" class="footer-support-link footer-support-zalo" title="Zalo: {{ $staff->zalo }}">
                                            <svg width="16" height="16" viewBox="0 0 50 50" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <path fill-rule="evenodd" clip-rule="evenodd" d="M22.782 0.166016H27.199C33.2653 0.166016 36.8103 1.05701 39.9572 2.74421C43.1041 4.4314 45.5875 6.89585 47.2557 10.0428C48.9429 13.1897 49.8339 16.7347 49.8339 22.801V27.1991C49.8339 33.2654 48.9429 36.8104 47.2557 39.9573C45.5685 43.1042 43.1041 45.5877 39.9572 47.2559C36.8103 48.9431 33.2653 49.8341 27.199 49.8341H22.8009C16.7346 49.8341 13.1896 48.9431 10.0427 47.2559C6.89583 45.5687 4.41243 43.1042 2.7442 39.9573C1.057 36.8104 0.166016 33.2654 0.166016 27.1991V22.801C0.166016 16.7347 1.057 13.1897 2.7442 10.0428C4.43139 6.89585 6.89583 4.41245 10.0427 2.74421C13.1707 1.05701 16.7346 0.166016 22.782 0.166016Z" fill="#0068FF"/>
                                                <path opacity="0.12" fill-rule="evenodd" clip-rule="evenodd" d="M49.8336 26.4736V27.1994C49.8336 33.2657 48.9427 36.8107 47.2555 39.9576C45.5683 43.1045 43.1038 45.5879 39.9569 47.2562C36.81 48.9434 33.265 49.8344 27.1987 49.8344H22.8007C17.8369 49.8344 14.5612 49.2378 11.8104 48.0966L7.27539 43.4267L49.8336 26.4736Z" fill="#001A33"/>
                                                <path fill-rule="evenodd" clip-rule="evenodd" d="M7.779 43.5892C10.1019 43.846 13.0061 43.1836 15.0682 42.1825C24.0225 47.1318 38.0197 46.8954 46.4923 41.4732C46.8209 40.9803 47.1279 40.4677 47.4128 39.9363C49.1062 36.7779 50.0004 33.22 50.0004 27.1316V22.7175C50.0004 16.629 49.1062 13.0711 47.4128 9.91273C45.7385 6.75436 43.2461 4.28093 40.0877 2.58758C36.9293 0.894239 33.3714 0 27.283 0H22.8499C17.6644 0 14.2982 0.652754 11.4699 1.89893C11.3153 2.03737 11.1636 2.17818 11.0151 2.32135C2.71734 10.3203 2.08658 27.6593 9.12279 37.0782C9.13064 37.0921 9.13933 37.1061 9.14889 37.1203C10.2334 38.7185 9.18694 41.5154 7.55068 43.1516C7.28431 43.399 7.37944 43.5512 7.779 43.5892Z" fill="white"/>
                                                <path d="M20.5632 17H10.8382V19.0853H17.5869L10.9329 27.3317C10.7244 27.635 10.5728 27.9194 10.5728 28.5639V29.0947H19.748C20.203 29.0947 20.5822 28.7156 20.5822 28.2606V27.1421H13.4922L19.748 19.2938C19.8428 19.1801 20.0134 18.9716 20.0893 18.8768L20.1272 18.8199C20.4874 18.2891 20.5632 17.8341 20.5632 17.2844V17Z" fill="#0068FF"/>
                                                <path d="M32.9416 29.0947H34.3255V17H32.2402V28.3933C32.2402 28.7725 32.5435 29.0947 32.9416 29.0947Z" fill="#0068FF"/>
                                                <path d="M25.814 19.6924C23.1979 19.6924 21.0747 21.8156 21.0747 24.4317C21.0747 27.0478 23.1979 29.171 25.814 29.171C28.4301 29.171 30.5533 27.0478 30.5533 24.4317C30.5723 21.8156 28.4491 19.6924 25.814 19.6924ZM25.814 27.2184C24.2785 27.2184 23.0273 25.9672 23.0273 24.4317C23.0273 22.8962 24.2785 21.645 25.814 21.645C27.3495 21.645 28.6007 22.8962 28.6007 24.4317C28.6007 25.9672 27.3685 27.2184 25.814 27.2184Z" fill="#0068FF"/>
                                                <path d="M40.4867 19.6162C37.8516 19.6162 35.7095 21.7584 35.7095 24.3934C35.7095 27.0285 37.8516 29.1707 40.4867 29.1707C43.1217 29.1707 45.2639 27.0285 45.2639 24.3934C45.2639 21.7584 43.1217 19.6162 40.4867 19.6162ZM40.4867 27.2181C38.9322 27.2181 37.681 25.9669 37.681 24.4124C37.681 22.8579 38.9322 21.6067 40.4867 21.6067C42.0412 21.6067 43.2924 22.8579 43.2924 24.4124C43.2924 25.9669 42.0412 27.2181 40.4867 27.2181Z" fill="#0068FF"/>
                                                <path d="M29.4562 29.0944H30.5747V19.957H28.6221V28.2793C28.6221 28.7153 29.0012 29.0944 29.4562 29.0944Z" fill="#0068FF"/>
                                                </svg>
                                        </a>
                                    @endif
                                </div>
                            </li>
                        @endforeach
                    </ul>
                @else
                    <div class="certification-box">
                        <a target="_blank" href="https://autosensor.vn">
                            <p>{{ 'Doanh nghiệp uy tín trong lĩnh vực tự động hóa công nghiệp' }}</p>
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
    <div class="footer-bottom">
        <div class="container">
            <p>© Copyright {{ date('Y') }}, {{ $settings->site_name ?? 'Bảo Tín JSC' }}</p>
        </div>
    </div>
</footer>

<!-- WhatsApp Float Button -->
@if($settings->whatsapp ?? null)
    <div class="whatsapp-float">
        <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $settings->whatsapp) }}" target="_blank">
            <i class="fab fa-whatsapp"></i>
        </a>
    </div>
@endif

<!-- Messenger Float Button -->
@if($settings->messenger ?? null)
    <div class="messenger-float">
        <a href="{{ $settings->messenger }}" target="_blank">
            <i class="fab fa-facebook-messenger"></i>
        </a>
    </div>
@endif

<!-- Chat Widget -->
<div class="chat-widget">
    <button class="back-to-top" id="back-to-top" title="Lên đầu trang">↑</button>
    <button class="chat-button" id="chat-button" title="Thông tin liên hệ">
        <svg width="40" height="40" xmlns:rdf="http://www.w3.org/1999/02/22-rdf-syntax-ns#" xmlns="http://www.w3.org/2000/svg" xmlns:cc="http://web.resource.org/cc/" xmlns:xlink="http://www.w3.org/1999/xlink" xmlns:dc="http://purl.org/dc/elements/1.1/" style="shape-rendering:geometricPrecision;text-rendering:geometricPrecision;image-rendering:optimizeQuality" fill-rule="evenodd" xml:space="preserve" clip-rule="evenodd" viewBox="0 0 58.282 58.282">
            <defs>
             <style type="text/css">
               .fil0 {fill:white}
               .fil1 {fill:white;fill-rule:nonzero}
             </style>
            </defs>
            <g id="Layer_x0020_1">
             <g id="_152222920">
              <path id="_152222248" class="fil0" d="m24.186 22.5c-5.4466 3.1445 2.8637 18.724 8.8237 15.283 1.0584 1.8332 4.4676 7.7381 5.526 9.5712-2.5146 1.4519-4.6432 2.3671-7.5585 0.70054-8.0946-4.6273-17.005-20.054-16.751-29.256 0.088046-3.196 1.971-4.4482 4.434-5.8702 1.0584 1.8332 4.4676 7.738 5.526 9.5712z"/>
              <path id="_150519168" class="fil0" d="m26.47 22.24c-0.58849 0.33978-1.3458 0.13697-1.6856-0.45158l-5.2095-9.0232c-0.33984-0.58862-0.13704-1.3459 0.45152-1.6857l2.738-1.5808c0.58855-0.33984 1.346-0.13685 1.6858 0.4517l5.2095 9.0231c0.33978 0.58849 0.13691 1.3458-0.45158 1.6856l-2.738 1.5808z"/>
              <path id="_226148072" class="fil0" d="m40.702 46.89c-0.58849 0.33978-1.3458 0.13697-1.6856-0.45164l-5.2095-9.0232c-0.33984-0.58855-0.13704-1.3459 0.45152-1.6856l2.738-1.5808c0.58855-0.33978 1.346-0.13679 1.6858 0.4517l5.2095 9.0231c0.33978 0.58855 0.13691 1.3459-0.45158 1.6856l-2.738 1.5808z"/>
             </g>
             <path class="fil1" d="m29.141-2.845e-15h0.0004319v0.006787c8.05 0.0001234 15.335 3.26 20.604 8.5296 5.2693 5.2693 8.5291 12.554 8.5293 20.604h0.006787v0.0013574h-0.006787c-0.0001234 8.05-3.26 15.335-8.5296 20.604-5.2694 5.2693-12.554 8.5291-20.604 8.5293v0.006787h-0.0013574v-0.006787c-8.05-0.0001234-15.335-3.26-20.604-8.5296-5.2693-5.2694-8.5291-12.554-8.5293-20.604h-0.006787v-0.0013574h0.006787c0.0001234-8.05 3.26-15.335 8.5296-20.604 5.2693-5.2693 12.554-8.5291 20.604-8.5293v-0.006787h0.0009255zm0.0004319 3.4758v0.006787h-0.0013574v-0.006787c-7.0792 0.0001851-13.494 2.8753-18.141 7.5226-4.6477 4.6476-7.5229 11.063-7.5229 18.142h0.006787v0.0013574h-0.006787c0.0001851 7.0792 2.8753 13.494 7.5226 18.141 4.6476 4.6477 11.063 7.5229 18.142 7.5229v-0.006787h0.0013574v0.006787c7.0792-0.0001851 13.494-2.8753 18.141-7.5226 4.6477-4.6476 7.5229-11.063 7.5229-18.142h-0.006787v-0.0013574h0.006787c-0.0001851-7.0792-2.8753-13.494-7.5226-18.141-4.6476-4.6477-11.063-7.5229-18.142-7.5229z"/>
            </g>
           <metadata><rdf:RDF><cc:Work><dc:format>image/svg+xml</dc:format><dc:type rdf:resource="http://purl.org/dc/dcmitype/StillImage"/><cc:license rdf:resource="http://creativecommons.org/licenses/publicdomain/"/><dc:publisher><cc:Agent rdf:about="http://openclipart.org/"><dc:title>Openclipart</dc:title></cc:Agent></dc:publisher></cc:Work><cc:License rdf:about="http://creativecommons.org/licenses/publicdomain/"><cc:permits rdf:resource="http://creativecommons.org/ns#Reproduction"/><cc:permits rdf:resource="http://creativecommons.org/ns#Distribution"/><cc:permits rdf:resource="http://creativecommons.org/ns#DerivativeWorks"/></cc:License></rdf:RDF></metadata></svg>
    </button>
    
    <!-- Contact Info Box -->
    <div class="contact-info-box" id="contact-info-box">
        <div class="contact-info-header">
            <h4>Thông tin liên hệ</h4>
            <button class="close-contact-box" id="close-contact-box">×</button>
        </div>
        <div class="contact-info-content">
            @if(isset($settings->site_name) && $settings->site_name)
                <div class="contact-item">
                    <strong>{{ $settings->site_name }}</strong>
                </div>
            @endif
            @if(isset($settings->contact_phone) && $settings->contact_phone)
                <div class="contact-item">
                    <span class="contact-label">📞 Hotline Chăm sóc khách hàng:</span>
                    <a href="tel:{{ preg_replace('/[^0-9]/', '', $settings->contact_phone) }}">{{ $settings->contact_phone }}</a>
                </div>
            @endif

            @if(isset($settings->contact_phone_technical) && $settings->contact_phone_technical)
                <div class="contact-item">
                    <span class="contact-label">📞 Hotline Tư vấn kỹ thuật:</span>
                    <a href="tel:{{ preg_replace('/[^0-9]/', '', $settings->contact_phone_technical) }}">{{ $settings->contact_phone_technical }}</a>
                </div>
            @endif

            @if(isset($settings->contact_zalo) && $settings->contact_zalo)
                <div class="contact-item">
                    <span class="contact-label">💬 Zalo Chăm sóc khách hàng:</span>
                    <a href="https://zalo.me/{{ preg_replace('/[^0-9]/', '', $settings->contact_zalo) }}" target="_blank">{{ $settings->contact_zalo }}</a>
                </div>
            @endif

            @if(isset($settings->contact_email) && $settings->contact_email)
                <div class="contact-item">
                    <span class="contact-label">✉️ Email:</span>
                    <a href="mailto:{{ $settings->contact_email }}">{{ $settings->contact_email }}</a>
                </div>
            @endif
            @if(isset($settings->contact_address) && $settings->contact_address)
                <div class="contact-item">
                    <span class="contact-label">📍 Địa chỉ:</span>
                    <span>{{ $settings->contact_address }}</span>
                </div>
            @endif
            
            @if(isset($settings->facebook_link) && $settings->facebook_link)
                <div class="contact-item">
                    <span class="contact-label">👤 Facebook:</span>
                    <a href="{{ $settings->facebook_link }}" target="_blank">Xem trang</a>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Zalo Widget -->
@if(isset($settings->contact_zalo) && $settings->contact_zalo)
    <a href="https://zalo.me/{{ preg_replace('/[^0-9]/', '', $settings->contact_zalo) }}" target="_blank" class="zalo-widget">
        <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 50 50" fill="none">
            <path fill-rule="evenodd" clip-rule="evenodd" d="M22.782 0.166016H27.199C33.2653 0.166016 36.8103 1.05701 39.9572 2.74421C43.1041 4.4314 45.5875 6.89585 47.2557 10.0428C48.9429 13.1897 49.8339 16.7347 49.8339 22.801V27.1991C49.8339 33.2654 48.9429 36.8104 47.2557 39.9573C45.5685 43.1042 43.1041 45.5877 39.9572 47.2559C36.8103 48.9431 33.2653 49.8341 27.199 49.8341H22.8009C16.7346 49.8341 13.1896 48.9431 10.0427 47.2559C6.89583 45.5687 4.41243 43.1042 2.7442 39.9573C1.057 36.8104 0.166016 33.2654 0.166016 27.1991V22.801C0.166016 16.7347 1.057 13.1897 2.7442 10.0428C4.43139 6.89585 6.89583 4.41245 10.0427 2.74421C13.1707 1.05701 16.7346 0.166016 22.782 0.166016Z" fill="#0068FF"/>
            <path opacity="0.12" fill-rule="evenodd" clip-rule="evenodd" d="M49.8336 26.4736V27.1994C49.8336 33.2657 48.9427 36.8107 47.2555 39.9576C45.5683 43.1045 43.1038 45.5879 39.9569 47.2562C36.81 48.9434 33.265 49.8344 27.1987 49.8344H22.8007C17.8369 49.8344 14.5612 49.2378 11.8104 48.0966L7.27539 43.4267L49.8336 26.4736Z" fill="#001A33"/>
            <path fill-rule="evenodd" clip-rule="evenodd" d="M7.779 43.5892C10.1019 43.846 13.0061 43.1836 15.0682 42.1825C24.0225 47.1318 38.0197 46.8954 46.4923 41.4732C46.8209 40.9803 47.1279 40.4677 47.4128 39.9363C49.1062 36.7779 50.0004 33.22 50.0004 27.1316V22.7175C50.0004 16.629 49.1062 13.0711 47.4128 9.91273C45.7385 6.75436 43.2461 4.28093 40.0877 2.58758C36.9293 0.894239 33.3714 0 27.283 0H22.8499C17.6644 0 14.2982 0.652754 11.4699 1.89893C11.3153 2.03737 11.1636 2.17818 11.0151 2.32135C2.71734 10.3203 2.08658 27.6593 9.12279 37.0782C9.13064 37.0921 9.13933 37.1061 9.14889 37.1203C10.2334 38.7185 9.18694 41.5154 7.55068 43.1516C7.28431 43.399 7.37944 43.5512 7.779 43.5892Z" fill="white"/>
            <path d="M20.5632 17H10.8382V19.0853H17.5869L10.9329 27.3317C10.7244 27.635 10.5728 27.9194 10.5728 28.5639V29.0947H19.748C20.203 29.0947 20.5822 28.7156 20.5822 28.2606V27.1421H13.4922L19.748 19.2938C19.8428 19.1801 20.0134 18.9716 20.0893 18.8768L20.1272 18.8199C20.4874 18.2891 20.5632 17.8341 20.5632 17.2844V17Z" fill="#0068FF"/>
            <path d="M32.9416 29.0947H34.3255V17H32.2402V28.3933C32.2402 28.7725 32.5435 29.0947 32.9416 29.0947Z" fill="#0068FF"/>
            <path d="M25.814 19.6924C23.1979 19.6924 21.0747 21.8156 21.0747 24.4317C21.0747 27.0478 23.1979 29.171 25.814 29.171C28.4301 29.171 30.5533 27.0478 30.5533 24.4317C30.5723 21.8156 28.4491 19.6924 25.814 19.6924ZM25.814 27.2184C24.2785 27.2184 23.0273 25.9672 23.0273 24.4317C23.0273 22.8962 24.2785 21.645 25.814 21.645C27.3495 21.645 28.6007 22.8962 28.6007 24.4317C28.6007 25.9672 27.3685 27.2184 25.814 27.2184Z" fill="#0068FF"/>
            <path d="M40.4867 19.6162C37.8516 19.6162 35.7095 21.7584 35.7095 24.3934C35.7095 27.0285 37.8516 29.1707 40.4867 29.1707C43.1217 29.1707 45.2639 27.0285 45.2639 24.3934C45.2639 21.7584 43.1217 19.6162 40.4867 19.6162ZM40.4867 27.2181C38.9322 27.2181 37.681 25.9669 37.681 24.4124C37.681 22.8579 38.9322 21.6067 40.4867 21.6067C42.0412 21.6067 43.2924 22.8579 43.2924 24.4124C43.2924 25.9669 42.0412 27.2181 40.4867 27.2181Z" fill="#0068FF"/>
            <path d="M29.4562 29.0944H30.5747V19.957H28.6221V28.2793C28.6221 28.7153 29.0012 29.0944 29.4562 29.0944Z" fill="#0068FF"/>
        </svg>
    </a>
@endif