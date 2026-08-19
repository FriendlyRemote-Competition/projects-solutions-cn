import {Link} from "react-router";

export const NotFound = ({title}) => {


    return <div className='flex w-full h-full gap-2 flex-col items-center justify-center'>
        <h2>{title || "Page"} Not Found...</h2>
        <Link className='primary px-4 py-2 rounded-lg' to={'/'}>Home</Link>
    </div>
}