import ElementMap from "Bon/App/UI/Common/ElementMap";
import FileUploaderFactory from "Bon/App/UI/Common/FileUploader/FileUploaderFactory";

export default class AvatarModalNajaExtension {
	/**
	 * @return {string}
	 */
	static get SELECTOR() {
		return '[data-control="App.User.Navigation.Avatar.AvatarModal"]';
	}

	/**
	 * @return {void}
	 */
	initialize(naja) {
		naja.addEventListener(
			'init',
			() => this.setup(document.body)
		);
		naja.snippetHandler.addEventListener(
			'beforeUpdate',
			(event) => this.dispose(event.detail.snippet)
		);
		naja.snippetHandler.addEventListener(
			'afterUpdate',
			(event) => this.setup(event.detail.snippet)
		);
	}

	/**
	 * @param {HTMLElement} html
	 * @return {void}
	 */
	setup(html) {
		html.querySelectorAll(this.constructor.SELECTOR).forEach(
			(controlElement) => {
				const uploader = this.createFileUploader(controlElement);

				ElementMap.set(controlElement, FileUploaderFactory.SELECTOR, uploader);
			}
		);
	}

	/**
	 * @param {HTMLElement} html
	 * @return {void}
	 */
	dispose(html) {
		html.querySelectorAll(this.constructor.SELECTOR).forEach(
			(controlElement) => {
				// file uploader
				if (ElementMap.has(controlElement, FileUploaderFactory.SELECTOR)) {
					const uploader = ElementMap.get(controlElement, FileUploaderFactory.SELECTOR);
					uploader.unbindAll();
					uploader.destroy();

					ElementMap.remove(controlElement, FileUploaderFactory.SELECTOR);
				}
			}
		);
	}

	/**
	 * @param {HTMLElement} controlElement
	 * @return {plupload.Uploader}
	 */
	createFileUploader(controlElement) {
		const browseButton = controlElement.querySelector('.btn[data-action="browse"]');
		const cancelButton = controlElement.querySelector('.btn[data-action="cancel"]');
		const removeButton = controlElement.querySelector('.btn[data-action="remove"]');
		const dropElement = controlElement.querySelector('.drop-area');
		const uploadProcessElement = controlElement.querySelector('.upload-process');
		const uploadedSpan = controlElement.querySelector('[data-placeholder="uploaded"]');
		const uploaderBlock = controlElement.querySelector(FileUploaderFactory.SELECTOR);

		const fileUploader = FileUploaderFactory.create(uploaderBlock);
		fileUploader.setOption('browse_button', browseButton);
		fileUploader.setOption('drop_element', dropElement);
		fileUploader.bind(
			'FilesAdded',
			(uploader, files) => {
				let file = files[0];

				uploadedSpan.innerHTML = file.percent + '%';
				cancelButton.click(
					(e) => {
						e.preventDefault();
						uploader.removeFile(file);
					}
				);

				dropElement.style.display = 'none';
				browseButton.style.display = 'none';

				if (removeButton) {
					removeButton.style.display = 'none';
				}

				uploadProcessElement.style.display = 'block';
				cancelButton.style.display = 'block';

				uploader.start();
				uploader.refresh();
			}
		);
		fileUploader.bind(
			'FilesRemoved',
			(uploader, files) => {
				uploadProcessElement.style.display = 'none';
				cancelButton.style.display = 'none';

				if (removeButton) {
					removeButton.style.display = 'flex';
				}

				dropElement.style.display = 'block';
				browseButton.style.display = 'block';

				uploader.refresh();
			}
		);
		fileUploader.bind(
			'UploadProgress',
			(uploader, file) => uploadedSpan.innerHTML = file.percent + '%'
		);
		fileUploader.bind(
			'Error',
			(uploader, error) => {
				console.log('Error ' + error.code + ': ' + error.message);
				uploader.refresh();
			}
		);
		fileUploader.init();

		return fileUploader;
	}
}