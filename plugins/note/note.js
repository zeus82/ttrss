/* global Plugins, xhrJson, Notify, fox, __ */

Plugins.Note = {
	edit: function(id) {
		const query = "backend.php?op=pluginhandler&plugin=note&method=edit&param=" + encodeURIComponent(id);

		const dialog = new fox.SingleUseDialog({
			id: "editNoteDlg",
			title: __("Edit article note"),
			execute: function () {
				if (this.validate()) {
					Notify.progress("Saving article note...", true);

					xhrJson("backend.php", this.attr('value'), (reply) => {
						Notify.close();
						dialog.hide();

						if (reply) {
							const elem = $("POSTNOTE-" + id);

							if (elem) {
								elem.innerHTML = reply.note;

								if (reply.raw_length != 0)
									Element.show(elem);
								else
									Element.hide(elem);
							}
						}
					});
				}
			},
			href: query,
		});

		dialog.show();
	}
};
