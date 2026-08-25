import { ICourse } from "@/interFace/interFace";
import Image from "next/image";
import Link from "next/link";

interface ICourseProps {
    adbox: ICourse;
}

const AdBoxCard = ({ adbox }: ICourseProps) => {
    return (
        <div className="bd-course-wrapper">
            <div className="bd-add-box">
                <div className="bd-add-box-top">
                    <h4 className="bd-add-box-title">{adbox.title}</h4>
                    <p className="bd-add-box-desc">{adbox.courseDescription}</p>
                </div>
                <div className="bd-add-box-bottom">
                    <div className="bd-add-box-brand">
                        <ul>
                            {adbox?.brands?.map((brand, index) => (
                                <li key={index}>
                                    <Image src={brand} alt="brand" />
                                </li>
                            ))}
                        </ul>
                    </div>
                    <div className="bd-add-box-btn">
                        <Link href="#" className="bd-btn btn-outline-primary">
                            {adbox.buttonText}
                        </Link>
                    </div>
                </div>
            </div>
        </div>
    );
};
export default AdBoxCard;  