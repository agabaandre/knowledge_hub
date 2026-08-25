import { ICourse } from "@/interFace/interFace";

const RenderTextContentTwo = (course: ICourse) => {
    if (course.courseTextContent) {
        if (course.courseTextContentStyle) {
            return (
                <div className="bd-course-text-content">
                    <div className="text fs-40 mb--5 text-white">{course.courseName}</div>
                    <div className="text fs-40 mb--5 text-white">{course.smallText}</div>
                    <div className="text fs-40 text-white">{course.smallTextTwo}</div>
                </div>
            );
        } else {
            return (
                <div className="bd-course-text-content">
                    <div className="text-3 fs-30 white-bg text-primary pl-10 pr-10 pt--5 pb--5 d-inline-block latter-sp-2 uppercase mb-30 filter-shadow radius-10">
                        {course.courseName}</div>
                    <div className="text fs-30 white-bg text-primary pl-10 pr-10 pt--5 pb--5 latter-sp-2 uppercase filter-shadow radius-10">
                        {course.smallText}</div>
                </div>
            );
        }
    } else {
        return (
            <>
                <div className="bd-course-text-content">
                    <div className="text-1 fs-50 text-white mb-25">{course.courseName}</div>
                    <div className="text-3 fs-30 white-bg text-primary pl-10 pr-10 pt--5 pb--5 d-inline-block latter-sp-2 uppercase filter-shadow radius-10">
                        {course.smallText}</div>
                </div>
            </>
        );
    }
};
export default RenderTextContentTwo;