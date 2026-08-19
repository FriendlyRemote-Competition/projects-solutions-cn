import dataJSON from '../assets/data.json'

/**
 *
 * @returns {any}
 */
export function getTheme() {

    if (!localStorage.theme) {
        localStorage.theme = "light";
    }
    return localStorage.theme;
}

/**
 *
 * @param theme
 */
export function saveTheme(theme) {
    localStorage.theme = theme;
}

/**
 *
 * @returns {any}
 */
export function getData() {
    const data = JSON.parse(localStorage.data || 'null');
    if (!data) {
        localStorage.data = JSON.stringify(dataJSON);
    }
    return JSON.parse(localStorage.data || 'null');
}


/**
 *
 * @param data
 */
export function saveData(data) {
    localStorage.data = JSON.stringify(data);
}


/**
 *
 * @returns {any}
 */
export function getFontSize() {
    if (!localStorage.fontSize) {
        localStorage.fontSize = "A";
    }
    return localStorage.fontSize;
}


/**
 *
 * @param fontSize
 * @returns {*}
 */
export function saveFontSize(fontSize) {
    return localStorage.fontSize = fontSize;
}


/**
 *
 * @returns {any}
 */
export function getLineSpace() {
    if (!localStorage.lineSpace) {
        localStorage.lineSpace = "1.5";
    }
    return localStorage.lineSpace;
}


/**
 *
 * @param lineSpace
 */
export function saveLineSpace(lineSpace) {

    localStorage.lineSpace = lineSpace.toString();
}

