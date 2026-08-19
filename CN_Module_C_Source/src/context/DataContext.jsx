import {createContext, useContext, useEffect, useState} from "react";
import {
    getData,
    getFontSize,
    getLineSpace,
    getTheme,
    saveData,
    saveFontSize,
    saveLineSpace,
    saveTheme
} from "../utils/tool.js";
import {SettingPanel} from "../components/SettingPanel.jsx";

const DataContext = createContext();


/**
 *
 * @returns {unknown}
 */
export const useData = () => useContext(DataContext);


export const DataContextProvider = ({children}) => {
    const [theme, setTheme] = useState(getTheme());
    const [settingModal, setSettingModal] = useState(false);
    const [fontSize, setFontSize] = useState(getFontSize());
    const [lineSpace, setLineSpace] = useState(getLineSpace())
    const [data, setData] = useState(getData())

    useEffect(() => {
        let dark = true;
        if (theme !== 'dark') {
            dark = false;
        }
        document.documentElement.classList.toggle("dark", dark);
        saveTheme(theme);
    }, [theme]);


    useEffect(() => {
        saveData(data);
    }, [data]);


    useEffect(() => {
        document.documentElement.classList.remove("font-small");
        document.documentElement.classList.remove("font-medium");
        document.documentElement.classList.remove("font-large");
        document.documentElement.classList.add(`${fontSize === "A" ? "font-medium" : fontSize === 'A-' ? "font-small" : "font-large"}`)
        saveFontSize(fontSize);
    }, [fontSize])

    useEffect(() => {
        saveLineSpace(lineSpace)
        document.documentElement.style.setProperty('--line-space-value', lineSpace.toString());
    }, [lineSpace]);

    const value = {
        onTheme: setTheme,
        theme, data,
        onData: setData,
        settingModal,
        onSettingModal: setSettingModal,
        fontSize,
        onFontSize: setFontSize,
        lineSpace,
        onLineSpace: setLineSpace,
    };

    return (
        <DataContext value={value}>
            {children}
            <SettingPanel onClose={() => setSettingModal(false)} open={settingModal}></SettingPanel>
        </DataContext>
    )
}