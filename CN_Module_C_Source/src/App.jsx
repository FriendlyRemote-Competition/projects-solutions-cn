import {RouterProvider} from "react-router";
import {router} from "./routes/router.jsx";
import {DataContextProvider} from "./context/DataContext.jsx";

function App() {

    return (
        <DataContextProvider>
            <RouterProvider router={router}></RouterProvider>
        </DataContextProvider>
    )
}

export default App
