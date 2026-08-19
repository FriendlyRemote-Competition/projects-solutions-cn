import {useMemo} from "react";
import {useNavigate} from "react-router";

/**
 *
 * @param param0
 * @param param0.chapter
 * @returns {React.JSX.Element}
 * @constructor
 */
export const ChapterItem = ({chapter}) => {
    const navigate = useNavigate();

    const Progress = useMemo(() => {
        const total = chapter.sections.length;

        const alreadyRead = chapter.sections.filter(v => v.isRead).length;

        return ((alreadyRead / total) * 100).toFixed(0);


    }, [chapter]);


    const CurReadId = useMemo(() => {
        return [...chapter.sections].reverse().find(v => v.isRead)?.id || chapter.sections[0].id;
    }, [chapter]);


    return (
        <article
            onClick={() => navigate(`/chapter/${chapter.id}/${CurReadId}`)}
            aria-label={`chapter ${chapter.number}. ${chapter.title}`}
            className='card border flex md:flex-row flex-col gap-2 md:items-center cursor-pointer justify-between border-slate-200 rounded-lg p-4 hover:shadow-lg'>
            <div className='flex items-center w-full gap-2'>
                <div className='flex flex-col gap-2'>
                    <h3>Chapter {chapter.number}. {chapter.title}</h3>
                    <div className='flex md:items-center flex-col w-full md:flex-row gap-2'>
                        <div
                            className='w-[40%] w-full h-[20px]  border dark:border-white/12 border-slate-200 rounded-full overflow-hidden'>
                            <div className={'bg-blue-500 h-full'} style={{
                                width: Progress + "%"
                            }}></div>
                        </div>
                        {
                            Progress == '100' ? <div className='text-teal-500 font-semibold text-center bg-teal-100  px-4 py-1 rounded-lg'>Completed</div> : (
                                <div className='text-nowrap px-4 py-1 rounded-lg text-center   text-blue-500 bg-blue-100 font-semibold'>{Progress}% read</div>
                            )
                        }
                    </div>
                    <span>{chapter.sections.length} Sections</span>
                </div>
            </div>
            <button className='primary'>{">"}</button>
        </article>
    )
}