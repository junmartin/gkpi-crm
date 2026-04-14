import './bootstrap';

import Alpine from 'alpinejs';

const allowedControlKeys = new Set([
	'Backspace',
	'Delete',
	'Tab',
	'Enter',
	'Escape',
	'ArrowLeft',
	'ArrowRight',
	'ArrowUp',
	'ArrowDown',
	'Home',
	'End',
]);

const sanitizePinValue = (value, maxLength) => value.replace(/\D+/g, '').slice(0, maxLength);

const setupPinInput = (input) => {
	const maxLength = Number.parseInt(input.getAttribute('maxlength') ?? '6', 10) || 6;

	input.setAttribute('inputmode', 'numeric');
	input.setAttribute('pattern', '[0-9]*');
	input.setAttribute('enterkeyhint', 'done');

	input.addEventListener('keydown', (event) => {
		if (event.ctrlKey || event.metaKey || event.altKey || allowedControlKeys.has(event.key)) {
			return;
		}

		if (!/^\d$/.test(event.key)) {
			event.preventDefault();
		}
	});

	input.addEventListener('beforeinput', (event) => {
		if (event.data !== null && /\D/.test(event.data)) {
			event.preventDefault();
		}
	});

	input.addEventListener('input', () => {
		const sanitizedValue = sanitizePinValue(input.value, maxLength);

		if (input.value !== sanitizedValue) {
			input.value = sanitizedValue;
		}
	});
};

document.addEventListener('DOMContentLoaded', () => {
	document.querySelectorAll('input[data-pin-input]').forEach(setupPinInput);
});

window.Alpine = Alpine;

Alpine.start();
