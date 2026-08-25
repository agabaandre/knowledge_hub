import { IReviewItem } from "@/interFace/dashboard-interface";
import Link from "next/link";

const ReviewItem: React.FC<IReviewItem> = ({ title, rating, reviewsCount, description }) => (
    <div className="bd-dashboard-review-item mb-30">
        <h5 className="title underline">
            <Link href="/courses/course-details">
                {title}
            </Link>
        </h5>
        <div className="course-info d-flex align-content-center justify-content-between flex-wrap gap-2 mb-10">
            <div className="bd-course-rating-wrap d-flex align-items-center gap-10">
                <div className="bd-course-rating-icon fs-14 d-flex rating-color">
                    {Array.from({ length: rating }).map((_, idx) => (
                        <i key={idx} className="fa-solid fa-star" />
                    ))}
                </div>
                <div className="bd-course-rating-text">
                    <span>{rating}.0 ({reviewsCount})</span>
                </div>
            </div>
            <div className="bd-button-action">
                <Link href="#" className="bd-default-tooltip edit">
                    <span>
                        <i className="fa-light fa-pen-to-square" />
                    </span>
                </Link>
                <Link href="#" className="bd-default-tooltip view">
                    <span>
                        <i className="fa-sharp fa-light fa-eye" />
                    </span>
                </Link>
                <Link href="#" className="bd-default-tooltip delete">
                    <span>
                        <i className="fa-light fa-trash-can" />
                    </span>
                </Link>
            </div>
        </div>
        <p>{description}</p>
    </div>
);

export default ReviewItem;