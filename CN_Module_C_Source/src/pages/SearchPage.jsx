import {Link, useSearchParams} from "react-router";
import {useData} from "../context/DataContext.jsx";
import {useMemo} from "react";

/**
 *
 * @returns {React.JSX.Element}
 * @constructor
 */
export const SearchPage = () => {
    const [searchParams] = useSearchParams();
    const {data} = useData();
    const search = searchParams.get('query') || "";

    const searchResults = useMemo(() => {
        if (!search.trim() || !data?.chapters) return [];
        const query = search.trim().toLowerCase();
        const results = [];
        for (const chapter of data.chapters) {
            for (const section of chapter.sections) {
                const headingText = section.heading.toLowerCase();
                const contentText = section.content.toLowerCase();

                const matchHeading = headingText.includes(query);
                const matchContent = contentText.includes(query);

                if (matchHeading || matchContent) {
                    let excerpt = "";
                    const rawText = matchHeading ? section.heading : section.content;
                    const idx = rawText.toLowerCase().indexOf(query);
                    const start = Math.max(0, idx - 30);
                    const end = Math.min(rawText.length, idx + query.length + 60);
                    excerpt = rawText.slice(start, end);
                    if (start > 0) excerpt = "…" + excerpt;
                    if (end < rawText.length) excerpt = excerpt + "…";

                    results.push({
                        sectionId: section.id,
                        chapterId: chapter.id,
                        sectionIndex: chapter.sections.findIndex(v => v.id == section.id) + 1,
                        chapterNumber: chapter.number,
                        chapterTitle: chapter.title,
                        sectionHeading: section.heading,
                        excerpt,
                        matchFromHeading: matchHeading
                    })
                }
            }
        }
        return results;
    }, [search, data]);

    return (
        <section className='!my-10 layout'>
            <h2 className='mb-5'>Search List</h2>
            <div className='flex flex-col gap-4'>
                {searchResults.map(item => (
                    <Link to={`/chapter/${item.chapterId}/${item.sectionId}?q=${search}`}
                          key={item.sectionId}
                          className="card border-slate-200 hover:shadow-lg cursor-pointer mb-4 p-3 border rounded">
                        <div className="text-sm text-gray-500 mb-1">
                            Chapter {item.chapterNumber} ➜ Section {item.sectionIndex}. {item.sectionHeading}
                        </div>
                        <h3 className="text-lg">
                            {item.sectionHeading}
                        </h3>
                        <p className="excerpt mt-2 text-gray‑700">
                            {item.excerpt}
                        </p>
                    </Link>
                ))}
                {
                    !searchResults.length && (
                        <div className='w-full text-center'>no results found</div>
                    )
                }
            </div>
        </section>
    )
}