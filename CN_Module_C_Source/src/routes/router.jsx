import {createBrowserRouter} from "react-router";
import {HomePage} from "../pages/HomePage.jsx";
import {Layout} from "../layouts/Layout.jsx";
import {ChapterDetailPage} from "../pages/ChapterDetailPage.jsx";
import {SearchPage} from "../pages/SearchPage.jsx";
import {NotFound} from "../components/NotFound.jsx";
import {BookmarkPage} from "../pages/BookmarkPage.jsx";

/**
 *
 * @type {Router}
 */
export const router = createBrowserRouter([
    {
        path: "/",
        element: <Layout/>,
        children: [
            {
                index: true,
                element: <HomePage/>
            },
            {
                path: "chapter/:chapterId/:sectionId",
                element: <ChapterDetailPage/>,
            },
            {
                path: "search",
                element: <SearchPage/>
            },
            {
                path: "bookmark",
                element:<BookmarkPage/>
            }
        ]
    },
    {
        path: "*",
        element: <NotFound/>
    }
], {
    basename: import.meta.env.DEV ? "/" : "/CN_Module_C"
});