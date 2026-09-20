(function () {
    // Mobil menü
    var toggle = document.querySelector('[data-sidebar-toggle]');
    var sidebar = document.querySelector('.sidebar');
    if (toggle && sidebar) {
        toggle.addEventListener('click', function () { sidebar.classList.toggle('open'); });
    }

    // Silme onayları
    document.querySelectorAll('form[data-confirm]').forEach(function (form) {
        form.addEventListener('submit', function (e) {
            if (!confirm(form.getAttribute('data-confirm'))) e.preventDefault();
        });
    });

    // Zengin metin editörü (Quill)
    var editorEl = document.getElementById('editor');
    var input = document.getElementById('content-input');
    var form = document.querySelector('[data-editor-form]');
    if (!editorEl || !input || !form || typeof Quill === 'undefined') return;

    var quill = new Quill('#editor', {
        theme: 'snow',
        placeholder: 'Yazınızı buraya yazın…',
        modules: {
            toolbar: {
                container: [
                    [{ header: [2, 3, false] }],
                    ['bold', 'italic', 'underline', 'strike'],
                    ['blockquote', { list: 'ordered' }, { list: 'bullet' }],
                    ['link', 'image'],
                    [{ align: [] }],
                    ['clean']
                ],
                handlers: { image: pickImage }
            }
        }
    });

    function pickImage() {
        var picker = document.createElement('input');
        picker.type = 'file';
        picker.accept = 'image/*';
        picker.onchange = function () {
            var file = picker.files[0];
            if (!file) return;
            var data = new FormData();
            data.append('image', file);
            data.append('_csrf', window.CSRF);
            fetch('/admin/yukle', { method: 'POST', body: data, credentials: 'same-origin' })
                .then(function (r) { return r.json(); })
                .then(function (res) {
                    if (res.url) {
                        var range = quill.getSelection(true);
                        quill.insertEmbed(range.index, 'image', res.url, 'user');
                        quill.setSelection(range.index + 1);
                    } else {
                        alert(res.error || 'Görsel yüklenemedi.');
                    }
                })
                .catch(function () { alert('Görsel yüklenemedi.'); });
        };
        picker.click();
    }

    form.addEventListener('submit', function () {
        var html = quill.getSemanticHTML ? quill.getSemanticHTML() : quill.root.innerHTML;
        input.value = html === '<p></p>' ? '' : html;
    });
})();
