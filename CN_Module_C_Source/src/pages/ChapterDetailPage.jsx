import {useData} from "../context/DataContext.jsx";
import {useEffect, useMemo} from "react";
import {useNavigate, useParams, useSearchParams} from "react-router";
import {NotFound} from "../components/NotFound.jsx";
import {highlight} from "../utils/parseMark.jsx";

/**
 *
 * @returns {React.JSX.Element}
 * @constructor
 */
export const ChapterDetailPage = () => {
    const {data, onData} = useData();
    const [searchParams] = useSearchParams();
    const {chapterId, sectionId} = useParams();
    const navigate = useNavigate();


    const mark = searchParams.get('q') || null;

    const CurChapter = useMemo(() => {
        return data.chapters.find(v => v.id == chapterId);
    }, [data, chapterId, sectionId])

    const CurSection = useMemo(() => {
        if (!CurChapter) return null;

        return CurChapter.sections.find(v => v.id == sectionId);
    }, [data, CurChapter, sectionId])


    /**
     * update the chapter to readed
     */
    useEffect(() => {
        onData(prev => {
            return {
                ...prev,
                chapters: prev.chapters.map(v => {

                    if (v.id == chapterId) {

                        return {
                            ...v,
                            sections: v.sections.map(section => {
                                if (section.id == sectionId) {
                                    return {
                                        ...section,
                                        isRead: true,
                                    }
                                }
                                return section;
                            })
                        }
                    }
                    return v;
                })
            }
        });
    }, [chapterId, sectionId]);


    const Progress = useMemo(() => {
        if (!CurChapter) return;
        const total = CurChapter.sections.length;

        const alreadyRead = CurChapter.sections.filter(v => v.isRead).length;

        return ((alreadyRead / total) * 100).toFixed(0);


    }, [CurChapter]);

    const curIndex = CurChapter?.sections?.findIndex(v => v.id == sectionId);

    if (!CurChapter) return <NotFound title={`Chapter ${chapterId}`}/>
    if (!CurSection) return <NotFound title={`Section ${sectionId}`}/>

    /**
     * return components
     */
    return (
        <>

            <section className='layout  !my-10'>
                <div className='flex gap-4 flex-wrap my-5'>
                    <div
                        className='w-[40%] w-full h-[20px]  dark:border-white/12 border border-slate-200 rounded-full overflow-hidden'>
                        <div className={'bg-blue-500 h-full'} style={{
                            width: Progress + "%"
                        }}></div>
                    </div>
                    <div className='text-nowrap text-center w-full dark:text-white/90'>Chapter Progress: {Progress}%</div>
                </div>
                <div className='grid  gap-4 lg:grid-cols-6 '>
                    <aside
                        className='p-4 rounded-lg lg:col-span-2 border border-slate-200 dark:border-white/8 dark:bg-white/4'>
                        <h2 className='mb-5'>TABLE OF CONTENTS</h2>
                        <ul role={'list'} className='flex flex-col gap-2'>
                            {
                                CurChapter.sections.map((section, i) => {

                                    return (
                                        <li
                                            role={'listitem'}
                                            onClick={() => navigate(`/chapter/${chapterId}/${section.id}`)}
                                            key={section.id}>
                                            <button
                                                onClick={() => navigate(`/chapter/${chapterId}/${section.id}`)}
                                                className={`flex w-full items-center gap-2 p-2 border rounded-lg cursor-pointer
            ${section.isRead ? "bg-slate-200 dark:bg-white/4" : ""}
            ${section.id === sectionId ? "bg-linear-180 from-blue-500 to-blue-600 !text-white" : ""}`}
                                            >
                                                {section.isRead && <span>✔</span>}
                                                <div className={`dark:text-white`}>{i + 1}. Section {i + 1}</div>
                                            </button>
                                        </li>
                                    )
                                })
                            }
                        </ul>
                    </aside>
                    <main
                        className='lg:col-span-4 p-4 rounded-lg border border-slate-200 dark:border-white/8 dark:bg-white/4'>
                        <h2>{highlight(CurSection.heading, mark)}</h2>
                        <p className='content-p'>{highlight(CurSection.content, mark)}</p>
                        <div className='min-h-[300px] overflow-hidden border border-slate-200 rounded-lg my-2'>
                            <img className='rounded-lg ' src={"/" + CurSection.image}
                                 alt={CurSection.imageAlt || `Chapter ${chapterId} Section ${sectionId} image`}/>
                        </div>
                        <div className='mt-auto flex items-center justify-between'>
                            <button className='secondary'
                                    onClick={() => navigate(`/chapter/${chapterId}/${CurChapter.sections[curIndex - 1]?.id}`)}
                                    disabled={curIndex <= 0}>Previous
                            </button>
                            <button
                                className='secondary'
                                disabled={curIndex >= CurChapter.sections.length - 1}
                                onClick={() => navigate(`/chapter/${chapterId}/${CurChapter.sections[curIndex + 1]?.id}`)}>Next
                            </button>
                        </div>
                    </main>
                </div>
            </section>
        </>
    )
}