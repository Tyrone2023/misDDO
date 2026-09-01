<!-- Footer Start -->
<footer class="footer">
    <div class="container-fluid">
        <div class="foot-inner">
            <span><span class="foot-brand">DOORS</span> &mdash; Davao de Oro Online Recruitment System &copy; <?= date('Y'); ?></span>
            <span class="nav-hide-xs">Department of Education &bull; Region XI &bull; Schools Division of Davao de Oro</span>
        </div>
    </div>
</footer>
<!-- end Footer -->

</div>

<!-- ============================================================== -->
<!-- End Page content -->
<!-- ============================================================== -->

</div>
<!-- END wrapper -->


<!-- Vendor js -->
<script src="<?= base_url(); ?>assets/js/vendor.min.js"></script>

<!-- Plugin js-->
<script src="<?= base_url(); ?>assets/libs/parsleyjs/parsley.min.js"></script>

<!-- Validation init js-->
<script src="<?= base_url(); ?>assets/js/pages/form-validation.init.js"></script>

<!-- App js -->
<script src="<?= base_url(); ?>assets/js/app.min.js"></script>

<script src="<?= base_url(); ?>assets/libs/custombox/custombox.min.js"></script>
                <script>
                    /*
                     * Device fingerprint for the audit trail.
                     *
                     * The device id cookie is the primary identifier; this is the
                     * corroborating one, so a device whose cookie was cleared can
                     * still be recognised. Stable, low-entropy signals only - no
                     * canvas or font probing, nothing that identifies a person
                     * rather than a machine.
                     */
                    (function () {
                        try {
                            if (document.cookie.indexOf('mis_dfp=') !== -1) return;

                            var n = window.navigator || {};
                            var s = window.screen || {};
                            var tz = '';
                            try { tz = Intl.DateTimeFormat().resolvedOptions().timeZone || ''; } catch (e) {}

                            var seed = [
                                n.userAgent || '',
                                n.platform || '',
                                n.language || '',
                                (n.languages || []).join(','),
                                n.hardwareConcurrency || '',
                                n.deviceMemory || '',
                                n.maxTouchPoints || '',
                                s.width + 'x' + s.height + 'x' + (s.colorDepth || ''),
                                new Date().getTimezoneOffset(),
                                tz
                            ].join('|');

                            // FNV-1a, twice over, for a 16-char hex digest. A hash
                            // is enough: the server only ever compares it.
                            function fnv(str, offset) {
                                var h = offset;
                                for (var i = 0; i < str.length; i++) {
                                    h ^= str.charCodeAt(i);
                                    h = (h + ((h << 1) + (h << 4) + (h << 7) + (h << 8) + (h << 24))) >>> 0;
                                }
                                return ('00000000' + h.toString(16)).slice(-8);
                            }

                            var fp = fnv(seed, 2166136261) + fnv(seed.split('').reverse().join(''), 16777619);

                            document.cookie = 'mis_dfp=' + fp
                                + ';path=/;max-age=' + (10 * 365 * 24 * 3600)
                                + ';SameSite=Lax'
                                + (location.protocol === 'https:' ? ';Secure' : '');
                        } catch (e) {
                            /* A missing fingerprint costs one corroborating signal, nothing more. */
                        }
                    })();
                </script>

</body>
</html>
