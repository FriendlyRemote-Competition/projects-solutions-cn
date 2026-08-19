import {NavLink} from "react-router";
import {SettingPanel} from "./SettingPanel.jsx";
import {useData} from "../context/DataContext.jsx";

/**
 *
 * @returns {React.JSX.Element}
 * @constructor
 */
export const Footer = () => {
    const {onSettingModal} = useData();


    return (
        <footer className='border-t sticky bottom-0 py-2 border-slate-200 mt-auto bg-slate-100'>
            <div className="layout flex w-full items-center gap-2">
                <NavLink to={'/'}
                         className={({isActive}) => `${isActive ? "bg-blue-500 text-white" : "hover:bg-blue-100"} w-full text-center rounded-lg  px-4 py-2 cursor-pointer`}>Home</NavLink>
                <NavLink to={'/bookmark'}
                         className={({isActive}) => `${isActive ? "bg-blue-500 text-white" : "hover:bg-blue-100"} w-full text-center rounded-lg   px-4 py-2 cursor-pointer`}>Bookmark</NavLink>
                <button className='secondary w-full' onClick={() => onSettingModal(true)}>Settings</button>
            </div>
        </footer>
    )
}