import {Outlet} from "react-router";
import {Footer} from "../components/Footer.jsx";
import {Header} from "../components/Header.jsx";

export const Layout = () => {


    return (
        <>
            <Header></Header>
            <main className={'h-full overflow-auto w-full'}>
                <Outlet/>
            </main>
            <Footer/>
        </>
    )
}