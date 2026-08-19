import {createPortal} from "react-dom";
import {useEffect, useState} from "react";
import {useData} from "../context/DataContext.jsx";

/**
 *
 * @param param0
 * @param param0.open
 * @param param0.onClose
 * @returns {React.ReactPortal|null}
 * @constructor
 */
export const SettingPanel = ({open, onClose}) => {
    const [isOpen, setIsOpen] = useState(open);
    const {fontSize, theme, lineSpace, onLineSpace, onTheme, onFontSize} = useData();
    const handleClose = () => {
        setIsOpen(false);
    }

    const handleTransitionEnd = () => {
        if (!isOpen) {
            onClose();
        }

    }

    useEffect(() => {
        setIsOpen(open);

    }, [open]);

    if (!open) return null;


    return (createPortal(<aside aria-label={'setting panel'} aria-modal={'true'} aria-hidden={'false'}
                                className='fixed top-0 left-0 w-full h-full  z-10'>
        <div
            onClick={handleClose}
            onTransitionEnd={handleTransitionEnd}
            style={{
                opacity: isOpen ? "1" : "0",
            }}
            className='absolute top-0 left-0 w-full h-full bg-black/40 duration-300'></div>
        <div
            style={{
                opacity: isOpen ? "1" : "0",
                transform: isOpen ? "translateX(0)" : "translateX(100%)"
            }}
            className='absolute p-4 flex gap-4 flex-col dark:bg-slate-900 dark:text-white  dark:border-white/8 top-0 z-1 duration-300 right-0 h-full w-[320px] border-l border-slate-200 bg-white'>
            <h2>Reading Settings</h2>
            <div className='flex gap-2 flex-col'>
                <h3>FONT SIZE</h3>
                <div className='flex items-center flex-wrap gap-4'>
                    <button
                        onClick={() => onFontSize('A-')}
                        className={`${fontSize === 'A-' ? "primary" : "secondary"}`}>A-
                    </button>
                    <button
                        onClick={() => onFontSize('A')}
                        className={`${fontSize === 'A' ? "primary" : "secondary"}`}>A
                    </button>
                    <button
                        onClick={() => onFontSize('A+')}
                        className={`${fontSize === 'A+' ? "primary" : "secondary"}`}>A+
                    </button>
                </div>
            </div>
            <div className='flex gap-2 flex-col'>
                <h3>THEME</h3>
                <div className='flex items-center gap-4'>
                    <button
                        onClick={() => onTheme('light')}
                        className={`${theme === 'light' ? "primary" : "secondary"}`}>Light
                    </button>
                    <button
                        onClick={() => onTheme('dark')}
                        className={`${theme === 'dark' ? "primary" : "secondary"}`}>Dark
                    </button>
                </div>
            </div>

            <div className='flex gap-2 flex-col'>
                <h3>LINE SPACE</h3>
                <div className='flex flex-col'>
                    <input
                        value={lineSpace}
                        onChange={e => onLineSpace(e.target.value)}
                        type="range"
                        min="0"
                        max="5"
                        step="0.5"
                    />
                    <span>{lineSpace}</span>
                </div>
            </div>


        </div>
    </aside>, document.body))
}