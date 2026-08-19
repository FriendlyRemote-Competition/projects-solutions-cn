import {useLocation, useMatch, useNavigate, useParams} from "react-router";
import {SearchInput} from "./SearchInput.jsx";
import {useData} from "../context/DataContext.jsx";
import {useMemo} from "react";

/**
 *
 * @returns {React.JSX.Element}
 * @constructor
 */
export const Header = () => {
    const {pathname} = useLocation();
    const {chapterId, sectionId} = useParams();
    const navigate = useNavigate();
    const {onData, data, onSettingModal, fontSize} = useData();
    const chapterMatch = useMatch("/chapter/:chapterId/:sectionId");

    /**
     *
     * @returns {*}
     */
    const handleBookmark = () => {
        if (!chapterMatch||!data) return;
        return onData(prev => {
            return {
                ...prev,
                chapters: prev.chapters.map(v => {
                    if (v.id == chapterId) {
                        return {
                            ...v,
                            sections: v.sections.map(section => {
                                if (sectionId == section.id) {
                                    return {
                                        ...section,
                                        isBookmark: !section.isBookmark,
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

    }

    const IsBookmark = useMemo(() => {
        if (!chapterMatch||!data) return;
        const chapter = data.chapters.find(v => v.id == chapterId) || {};

        const section = chapter?.sections?.find(v => v.id == sectionId);

        return section?.isBookmark || false;
    }, [data, sectionId, chapterId, chapterMatch]);


    const CurChapter = useMemo(() => {
        if (!chapterMatch||!data) return;
        const chapter = data.chapters.find(v => v.id == chapterId) || {};
        return chapter;
    }, [data, sectionId, chapterId, chapterMatch])


    return (
        <header className='border-b border-slate-200 bg-slate-50 py-2'>
            <div className="layout md:flex-row flex-col gap-2 flex items-center justify-between">
                {
                    pathname === '/' && (
                        <h1 className='flex items-center gap-2'>
                            <span>E-BOOK</span>
                        </h1>
                    )
                }
                {
                    chapterMatch && (
                        <>
                            <button onClick={() => navigate('/')} className='secondary'>← Library</button>
                            <span>Chapter {CurChapter.number} {"->"} Section {CurChapter.sections.findIndex(v => v.id == sectionId) + 1} of {CurChapter.sections.length}</span>
                        </>
                    )
                }
                <div className={`flex ${pathname === '/search' ? "w-full" : ""} md:flex-row w-full md:w-auto flex-col md:items-center gap-2`}>
                    <SearchInput/>
                    {
                        pathname === '/' && (
                            <button className='primary' onClick={() => onSettingModal(true)}>Settings</button>
                        )
                    }
                    {
                        chapterMatch && (
                            <>
                                <button onClick={handleBookmark}
                                        className={`${IsBookmark ? "primary" : "secondary"}`}>Bookmark
                                </button>
                                <button className='secondary'>{fontSize}</button>
                            </>
                        )
                    }
                </div>
            </div>
        </header>
    )
}