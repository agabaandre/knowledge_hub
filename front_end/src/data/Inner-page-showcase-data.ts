
import aboutOnline from "../../public/assets/images/landing-page/inner-page/about-online.webp";
import aboutUni from "../../public/assets/images/landing-page/inner-page/about-uni.webp";
import aboutModern from "../../public/assets/images/landing-page/inner-page/about-modern.webp";
import apply from "../../public/assets/images/landing-page/inner-page/apply.webp";
import becomeInstructor from "../../public/assets/images/landing-page/inner-page/become-instructor.webp";
import contact from "../../public/assets/images/landing-page/inner-page/contact.webp";
import blog from "../../public/assets/images/landing-page/inner-page/blog.webp";
import blogDetails from "../../public/assets/images/landing-page/inner-page/blog-details.webp";
import scholarships from "../../public/assets/images/landing-page/inner-page/scholarships.webp";
import shop from "../../public/assets/images/landing-page/inner-page/shop.webp";
import shopDetails from "../../public/assets/images/landing-page/inner-page/shop-details.webp";
import signIn from "../../public/assets/images/landing-page/inner-page/sign-in.webp";
import blogGridImg from "../../public/assets/images/landing-page/inner-page/blog-grid.webp";
import blogListImg from "../../public/assets/images/landing-page/inner-page/blog-list.webp";
import bookDetailsImg from "../../public/assets/images/landing-page/inner-page/book-details.webp";
import campusImg from "../../public/assets/images/landing-page/inner-page/campus.webp";
import eventImg from "../../public/assets/images/landing-page/inner-page/event.webp";
import eventDetailsImg from "../../public/assets/images/landing-page/inner-page/event-details.webp";
import historyImg from "../../public/assets/images/landing-page/inner-page/history.webp";
import instructorImg from "../../public/assets/images/landing-page/inner-page/instructor.webp";
import instructorDetailsImg from "../../public/assets/images/landing-page/inner-page/instructor-details.webp";
import pricingPlanImg from "../../public/assets/images/landing-page/inner-page/price.webp";
import signUpImg from "../../public/assets/images/landing-page/inner-page/sign-up.webp";
import termsConditionsImg from "../../public/assets/images/landing-page/inner-page/terms.webp";
import tuitionFeesImg from "../../public/assets/images/landing-page/inner-page/tuition-fees.webp";
import webinarDetailsImg from "../../public/assets/images/landing-page/inner-page/webinar.webp";
import { StaticImageData } from "next/image";

interface IDemoItemsSlider {
    title: string;
    link: string;
    imgSrc: StaticImageData;
}

export const demoItemsSliderOneData: IDemoItemsSlider[] = [
    { title: "about online course", link: "/about-online-course", imgSrc: aboutOnline },
    { title: "about university", link: "/about-university", imgSrc: aboutUni },
    { title: "about modern schooling", link: "/about-modern-schooling", imgSrc: aboutModern },
    { title: "application forms", link: "/apply-online", imgSrc: apply },
    { title: "become instructor", link: "/become-instructor", imgSrc: becomeInstructor },
    { title: "contact", link: "/contact-us", imgSrc: contact },
    { title: "blog", link: "/blog", imgSrc: blog },
    { title: "blog details", link: "/blog/blog-details", imgSrc: blogDetails },
    { title: "scholarships financial aid details", link: "/scholarships-financial-aid-details", imgSrc: scholarships },
    { title: "shop", link: "/shop", imgSrc: shop },
    { title: "shop details", link: "/shop/shop-details-two", imgSrc: shopDetails },
    { title: "sign in", link: "/sign-in", imgSrc: signIn },
];
export const demoItemsSliderTwoData: IDemoItemsSlider[] = [
    { title: "blog grid", link: "/blog-grid", imgSrc: blogGridImg },
    { title: "blog list", link: "/blog-list", imgSrc: blogListImg },
    { title: "book details", link: "/shop/shop-details", imgSrc: bookDetailsImg },
    { title: "campus", link: "/campus", imgSrc: campusImg },
    { title: "event", link: "/event", imgSrc: eventImg },
    { title: "event details", link: "/event/event-details", imgSrc: eventDetailsImg },
    { title: "history", link: "/history", imgSrc: historyImg },
    { title: "instructor", link: "/instructor", imgSrc: instructorImg },
    { title: "instructor details", link: "/instructor/instructor-details", imgSrc: instructorDetailsImg },
    { title: "pricing plan", link: "/pricing-table", imgSrc: pricingPlanImg },
    { title: "sign up", link: "/sign-up", imgSrc: signUpImg },
    { title: "terms conditions", link: "/terms-conditions", imgSrc: termsConditionsImg },
    { title: "tuition fees", link: "/tuition-fees", imgSrc: tuitionFeesImg },
    { title: "webinar details", link: "/webinar-details", imgSrc: webinarDetailsImg },
];