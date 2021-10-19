(function() {
  function iconForm(form) {
    const IconForm = Object.assign({}, {
      init: function(form) {
        this.form = form
        this.sendAttachementBackup = wp.media.editor.send.attachment;
        this.button = form.querySelector(".dt-genmapper__icon-upload-button");
        this.resetButton = form.querySelector(".dt-genmapper__icon-reset-button");
        this.field = form.querySelector(".dt-genmapper__icon-url-field");
        this.image = form.querySelector(".dt-genmapper__icon-image");
        this.default = form.dataset.iconDefault;
        this.button.addEventListener('click', this.upload.bind(this));
        this.resetButton.addEventListener('click', this.reset.bind(this));
      },

      upload: function() {
        wp.media.editor.send.attachment = this.sendAttachment.bind(this)
        wp.media.editor.open(this.button);
      },

      sendAttachment: function(props, attachment) {
        this.image.src = attachment.url;
        this.field.value = attachment.id;
        wp.media.editor.send.attachment = this.sendAttachementBackup;
        this.submit()
      },

      reset: function() {
        const answer = confirm('Are you sure?');
        if (answer) {
          this.image.src = this.default;
          this.field.value = null;
        }
        this.submit()
      },

      submit: function() {
        const data = new URLSearchParams();
        for (const pair of new FormData(this.form)) {
          data.append(pair[0], pair[1]);
        }

        fetch(`${window.wpApiGenmapper.root}dt-genmapper/v1/icon`, {
          method: 'POST',
          headers: {
            'X-WP-Nonce': window.wpApiGenmapper.nonce
          },
          body: new FormData(this.form)
        })
          .then(response => response.json())
          .then(data => console.log(data));
      }
    })
    IconForm.init(form)
  }

  document.querySelectorAll('.dt-genmapper__icon-form').forEach(iconForm)
})();
