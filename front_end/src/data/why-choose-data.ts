import { ISchoolingWhyChoose, WhyChooseDataType } from "@/interFace/interFace";
import facultyIcon from '../../public/assets/images/icon/faculty.svg';
import bookIcon from '../../public/assets/images/icon/book-2.svg';
import bulbIcon from '../../public/assets/images/icon/bulb.svg';
import globeIcon from '../../public/assets/images/icon/globe.svg';
import SpecializedCourseSvg from "@/svg/why-choose-svgs/SpecializedCourseSvg";
import FacultySvg from "@/svg/why-choose-svgs/FacultySvg";
import InnovativeLearningMethodSvg from "@/svg/why-choose-svgs/InnovativeLearningMethodSvg";
import IndustryConnectionSvg from "@/svg/why-choose-svgs/IndustryConnectionSvg";
import PersonalizedSupportSvg from "@/svg/why-choose-svgs/PersonalizedSupportSvg";
import RecognizedCertificationSvg from "@/svg/why-choose-svgs/RecognizedCertificationSvg";

export const whyChooseData: WhyChooseDataType[] = [
    {
        id: 1,
        icon: facultyIcon,
        title: 'World-Class Faculty',
        description: 'Learn from industry-leading experts, combining real-world experience with innovative teaching techniques.',
    },
    {
        id: 2,
        icon: bookIcon,
        title: 'Cutting-Edge Curriculum',
        description: 'Our curriculum is updated regularly to align with the latest industry trends and demands.',
    },
    {
        id: 3,
        icon: bulbIcon,
        title: 'Innovative Learning',
        description: 'We provide an engaging learning environment supported by advanced technology and modern resources.',
    },
    {
        id: 4,
        icon: globeIcon,
        title: 'Global Community',
        description: 'Become part of a diverse, global student body that fosters inclusive growth and cultural exchange.',
    }
]

export const schoolingWhyChooseData:ISchoolingWhyChoose[] = [
    {
        id: 1,
        icon: SpecializedCourseSvg,
        title: 'Specialized Courses',
        description: 'Our curriculum specialized courses tailored to meet the demands of various industries, ensuring that our students gain relevant knowledge.',
        wowDelayDuration:".3s"
    },
    {
        id: 2,
        icon: FacultySvg,
        title: 'Expert Faculty',
        description: 'Learn from industry experts and experienced educators who bring real-world experience and cutting-edge knowledge to the classroom.',
         wowDelayDuration:".3s"
    },
    {
        id: 3,
        icon: InnovativeLearningMethodSvg,
        title: 'Innovative Learning Methods',
        description: 'We employ innovative teaching methodologies and the latest technology to enhance the learning experience and keep students engaged.',
         wowDelayDuration:".3s"
    },
    {
        id: 4,
        icon: IndustryConnectionSvg,
        title: 'Strong Industry Connections',
        description: 'We have established strong connections with leading companies, providing students with valuable networking and internship opportunities',
         wowDelayDuration:".4s"
    },
    {
        id: 5,
        icon: PersonalizedSupportSvg,
        title: 'Personalized Support',
        description: 'We offer personalized support and mentorship to help each student achieve their academic and career goals.',
         wowDelayDuration:".4s"
    },
    {
        id: 6,
        icon: RecognizedCertificationSvg,
        title: 'Recognized Certifications',
        description: 'Our programs that are recognized by industry leaders, enhancing the value of your qualifications your career prospects.',
         wowDelayDuration:".4s"
    }
]
