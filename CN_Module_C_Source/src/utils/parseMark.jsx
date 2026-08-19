/**
 *
 * @param text
 * @param keyword
 * @returns {*}
 */
export function highlight(text, keyword) {
    if (!keyword?.trim()) return text;
    if (!text) return text;
    const reg = new RegExp(`(${keyword})`, "gi");
    const parts = text.split(reg);
    return parts.map((chunk, i) =>
        chunk.toLowerCase() === keyword.toLowerCase()
            ? <mark key={i}>{chunk}</mark>
            : chunk
    );
}