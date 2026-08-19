import {useData} from "../context/DataContext.jsx";
import {ChapterItem} from "../components/ChapterItem.jsx";


/**
 *
 * @returns {React.JSX.Element}
 * @constructor
 */
export const HomePage = () => {
    const {data} = useData();

    return (
        <section className='layout !my-10'>
            <h2>Chapter List</h2>
            <div className='my-10  grid gap-4 grid-cols-1'>
                {
                    data?.chapters?.map(chapter => {


                        return (
                            <ChapterItem chapter={chapter} key={chapter.id}/>
                        )
                    })
                }
            </div>
        </section>
    )
}