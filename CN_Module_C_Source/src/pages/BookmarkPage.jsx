import {useMemo} from "react";
import {useData} from "../context/DataContext.jsx";
import {Link} from "react-router";

/**
 *
 * @returns {React.JSX.Element}
 * @constructor
 */
export const BookmarkPage = () => {
    const {data} = useData();

    const myBookmarks = useMemo(() => {
        let results = [];
        data.chapters.forEach((chapter) => {
            chapter.sections.forEach((section, i) => {
                let excerpt = "";
                const rawText = section.content;
                const idx = rawText.toLowerCase().indexOf(section.content[0]);
                const start = Math.max(0, idx - 30);
                const end = Math.min(rawText.length, idx + 60);
                excerpt = rawText.slice(start, end);
                if (start > 0) excerpt = "…" + excerpt;
                if (end < rawText.length) excerpt = excerpt + "…";
                if (section.isBookmark) {
                    results.push({
                        sectionId: section.id,
                        chapterId: chapter.id,
                        sectionTitle: `Section ${i + 1}`,
                        chapterTitle: `Chapter ${chapter.number}`,
                        excerpt,
                    });
                }
            });
        })
        return results;
    }, [data]);


    return (
        <section className='layout !my-10'>
            <h2 className={'mb-5'}>My Bookmarks</h2>
            <div className='flex flex-col gap-4'>
                {
                    myBookmarks.map(item => {

                        return (
                            <Link to={`/chapter/${item.chapterId}/${item.sectionId}`}
                                  key={item.sectionId}
                                  className="card border-slate-200 hover:shadow-lg cursor-pointer mb-4 p-3 border rounded">
                                <div className="text-sm text-gray-500 mb-1">
                                    {item.chapterTitle} ➜ {item.sectionTitle}
                                </div>
                                <p className="excerpt mt-2 text-gray‑700">
                                    {item.excerpt}
                                </p>
                            </Link>
                        )
                    })
                }
                {
                    !myBookmarks.length && (
                        <div className='w-full text-center'>
                            <span>You don’t have any bookmarks yet</span>, you can read a chapter section and mark it.
                        </div>
                    )
                }
            </div>
        </section>
    )
}