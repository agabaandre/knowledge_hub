import { ICourse } from "@/interFace/interFace";

const   RenderTextContentFour = (item: ICourse) => {
    if (item.courseTextContent) {
        if (item.courseTextContentStyle) {
            return (
                <div className="bd-course-text-content">
                    <div className={`text-1 ${item.FontSizeClass? item.FontSizeClass :"fs-50"} ${item.spacingClass} text-white ${item.latterUppercase == true ? "uppercase":""}`}>{item.courseName}</div>
                    <div className={`text-1 ${item.FontSizeClassTwo} ${item.spacingClass} text-white ${item.latterUppercase == true ? "uppercase":""}`}>{item.smallText}</div>
                    <div className={`text-1 ${item.FontSizeClass? item.FontSizeClass :"fs-50"} ${item.spacingClass} text-white ${item.latterUppercase == true ? "uppercase":""}`}>{item.smallTextTwo}</div>
                </div>
            );
        } else {
            return (
                <div className="bd-course-text-content">
                    <div className="text-1 fs-50 mb--5 text-white">{item.courseName}</div>
                    <div className={`text ${item.FontSizeClass} ${item.spacingClass} text-white`}>{item.smallText}</div>
                    <div className={`text ${item.FontSizeClassTwo} white-bg text-primary pl-10 pr-10 pt--5 pb--5 d-inline-block latter-sp-2 uppercase`}>
                        {item.courseTag}
                    </div>
                </div>
            );
        }
    } else {
        return (
            <>
                <div className="small-text bg-text-color">{item.smallText}</div>
                <div className={`bd-course-overly-title ${item.FontSizeClass} ${item.courseTitleClass}`}>{item.courseName}</div>
            </>
        );
    }
};
export default RenderTextContentFour;