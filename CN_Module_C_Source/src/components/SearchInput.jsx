import {useLocation, useNavigate, useSearchParams} from "react-router";

export const SearchInput = () => {
    const [searchParams] = useSearchParams();
    const navigate = useNavigate();
    const {pathname} = useLocation();
    const handleSubmit = (e) => {
        e.preventDefault();
        const url = new URLSearchParams(searchParams);
        url.set("query", e.target.search.value)
        navigate(`/search?${url}`)
    }

    return (
        <form onSubmit={handleSubmit} className={`${pathname === '/search' ? "w-full" : ""}`}>
            <label>
                <input

                    aria-label={'enter some word to search chapter section'}
                    name={'search'} className='w-full'
                    type="search"
                    placeholder={pathname === '/search' ? `Seach the textbook: "${searchParams.get('query') || ''}"` : 'Search...'}/>
            </label>
        </form>
    )
}