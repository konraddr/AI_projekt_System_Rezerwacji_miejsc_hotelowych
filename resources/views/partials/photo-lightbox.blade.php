@once
    <div class="modal fade" id="hotelPhotoLightbox" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-xl">
            <div class="modal-content bg-dark border-0">
                <div class="modal-header border-0 pb-0">
                    <button type="button" class="btn-close btn-close-white ms-auto" data-bs-dismiss="modal"
                            aria-label="Zamknij"></button>
                </div>
                <div class="modal-body p-2 pt-0 text-center">
                    <img id="hotelPhotoLightboxImg" src="" class="hotel-photo-lightbox-img" alt="">
                </div>
            </div>
        </div>
    </div>
@endonce

@pushOnce('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const modalEl = document.getElementById('hotelPhotoLightbox');
            const modalImg = document.getElementById('hotelPhotoLightboxImg');

            if (!modalEl || !modalImg) {
                return;
            }

            modalEl.addEventListener('show.bs.modal', function (event) {
                const trigger = event.relatedTarget;

                if (!trigger) {
                    return;
                }

                modalImg.src = trigger.dataset.fullSrc || trigger.src;
                modalImg.alt = trigger.alt || '';
            });

            modalEl.addEventListener('hidden.bs.modal', function () {
                modalImg.removeAttribute('src');
                modalImg.alt = '';
            });
        });
    </script>
@endPushOnce
