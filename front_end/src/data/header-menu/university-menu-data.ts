
import { MenuItem } from "@/interFace/interFace";
import homeOneImg from "../../../public/assets/images/menu/menu-home-1.webp";
import homeTowImg from "../../../public/assets/images/menu/menu-home-2.webp";
import homeThreeImg from "../../../public/assets/images/menu/menu-home-3.webp";
import homeFourImg from "../../../public/assets/images/menu/menu-home-4.webp";
import homeFiveImg from "../../../public/assets/images/menu/menu-home-5.webp";
import homeSixImg from "../../../public/assets/images/menu/menu-home-6.webp";
import homeSevenImg from "../../../public/assets/images/menu/menu-home-7.webp";
import coummingSoonImg from "../../../public/assets/images/menu/menu-home-soon.webp";

const university_menu_data:MenuItem[] = [
  {
    id: 1,
    hasDropdown: false,
    children: false,
    active: true,
    title: "Home",
    pluseIncon: true,
    link: "#",
    previewImg: true,
    submenus: [
      { title: "Online Course", link: "/online-course", previewImg: homeOneImg },
      { title: "University", link: "/university", previewImg: homeTowImg },
      { title: "Modern Schooling", link: "/modern-schooling", previewImg: homeThreeImg },
      { title: "Kindergarten", link: "/kindergarten", previewImg: homeFourImg },
      { title: "Quran Learning", link: "/quran-learning", previewImg: homeFiveImg },
      { title: "Book Store", link: "/book-store", previewImg: homeSixImg },
      { title: "Language Academy", link: "/language-academy", previewImg: homeSevenImg },
      { title: "Kitchen Coach - Soon", link: "#", previewImg: coummingSoonImg },
      { title: "Marketplace - Soon", link: "#", previewImg: coummingSoonImg },
      { title: "Course Hub - Soon", link: "#", previewImg: coummingSoonImg },
    ],
  },
  {
    id: 2,
    hasDropdown: true,
    active: true,
    megaMenu: false,
    children: true,
    title: "Academics",
    pluseIncon: true,
    link: "#",
    submenus: [
      {
        title: "About University",
        link: "/about-university",
        pluseIncon: false,
      },
      {
        title: "Campus",
        link: "/campus",
        pluseIncon: false,
      },
      {
        title: "History",
        link: "/history",
        pluseIncon: false,
      },
      {
        title: "Executive Leaders",
        link: "/executive-leaders",
        pluseIncon: false,
      },
      {
        title: "Faculty Members",
        link: "/faculty-members",
        pluseIncon: false,
      },
      {
        title: "Vision, Mission & Strategy",
        link: "/mvs",
        pluseIncon: false,
      },
    ],
  },
  {
    id: 3,
    hasDropdown: true,
    active: true,
    megaMenu: false,
    children: true,
    title: "Admission",
    pluseIncon: true,
    link: "#",
    submenus: [
      {
        title: "University Program",
        link: "/courses-grid-two",
        pluseIncon: false,
      },
      {
        title: "Academic Calendar",
        link: "/academic-calendar",
        pluseIncon: false,
      },
      {
        title: "Tuition and Other Fees",
        link: "/tuition-fees",
        pluseIncon: false,
      },
      {
        title: "Scholarships & Financial Aid",
        link: "/scholarships-financial-aid",
        pluseIncon: false,
      },
      {
        title: "Scholarships Details",
        link: "/scholarships-financial-aid-details",
        pluseIncon: false,
      },
      {
        title: "Apply Online",
        link: "/apply-online",
        pluseIncon: false,
      },
    ],
  },
  {
    id: 4,
    hasDropdown: true,
    children: false,
    megaMenu: true,
    active: true,
    title: "Pages",
    pluseIncon: true,
    pageLayout: true,
    columnFour: true,
    link: "#",
    submenus: [
      {
        title: "Page Layout 1",
        link: "#",
        megaMenu: [
          { title: "Contact Us", link: "/contact-us" },
          { title: "About Online Course", link: "/about-online-course" },
          { title: "About Modern Schooling", link: "/about-modern-schooling" },
          { title: "About Kindergarten", link: "/about-kindergarten" },
          { title: "About Quarn Learning", link: "/about-quran-learning" },
          { title: "Instructor", link: "/instructor" },
          { title: "Instructor Details", link: "/instructor/instructor-details" },
          { title: "Become Instructor", link: "/become-instructor" },
          { title: "Event Grid", link: "/event" },
          { title: "Event List", link: "/event-list" },
          { title: "Event Details", link: "/event/event-details" },
          { title: "Webinar Details", link: "/webinar-details" },
        ]
      },
      {
        title: "Page Layout 2",
        link: "#",
        megaMenu: [
          { title: "Courses", link: "/courses" },
          { title: "Courses Search Filter", link: "/courses-filter-search" },
          { title: "Courses Filter Category", link: "/courses-filter-category" },
          { title: "Courses Grid", link: "/courses-grid" },
          { title: "Courses Grid Three", link: "/courses-grid-three" },
          { title: "Courses Grid Four", link: "/courses-grid-four" },
          { title: "Courses Grid Five", link: "/courses-grid-five" },
          { title: "Courses Grid Right Sidebar", link: "/courses-grid-right" },
          { title: "Courses Grid Left Sidebar", link: "/courses-grid-left" },
          { title: "Courses Left Sidebar", link: "/courses-list-one" },
          { title: "Courses Right Sidebar", link: "/courses-list-two" },
          { title: "No Sidebar", link: "/courses-list-three" },
        ]
      },
      {
        title: "Page Layout 3",
        link: "#",
        megaMenu: [
          { title: "Courses Details", link: "/courses/course-details" },
          { title: "Courses Details Two", link: "/courses/course-details-two" },
          { title: "Courses Details Three", link: "/courses/course-details-three" },
          { title: "Kindergarten Program", link: "/kindergarten-program-details" },
          { title: "Courses Lesson", link: "/course-lesson" },
          { title: "Create New Course", link: "/create-course" },
          { title: "Faq's", link: "/faq" },
          { title: "Pricing Table", link: "/pricing-table" },
          { title: "Shop", link: "/shop" },
          { title: "Shop V2", link: "/shop-v2" },
          { title: "Book Details", link: "/shop/shop-details" },
          { title: "Shop Details", link: "/shop/shop-details-two" },
        ]
      },
      {
        title: "Page Layout 4",
        link: "#",
        megaMenu: [
          { title: "Checkout", link: "/checkout" },
          { title: "Cart", link: "/cart" },
          { title: "Wishlist", link: "/wishlist" },
          { title: "Sign In", link: "/sign-in" },
          { title: "Sign Up", link: "/sign-up" },
          { title: "Forgot Password", link: "/forgot" },
          { title: "404 Page", link: "/error-page" },
          { title: "Coming Soon", link: "/coming-soon" },
          { title: "Under Maintenance", link: "/under-maintenance" },
          { title: "Terms And Conditions", link: "/terms-conditions" },
          { title: "Privacy & Policy", link: "/privacy-policy" },
        ]
      },
    ]
  },
  {
    id: 5,
    hasDropdown: true,
    children: true,
    megaMenu: false,
    active: true,
    title: "Elements",
    pluseIncon: true,
    link: "#",
    submenus: [
      {
        title: "Style Guide",
        link: "/style-guide",
        pluseIncon: false,
      },
      {
        title: "Badge",
        link: "/element-badge",
        pluseIncon: false,
      },
      {
        title: "Blog",
        link: "/element-blog",
        pluseIncon: false,
      },
      {
        title: "Categories",
        link: "/element-categories",
        pluseIncon: false,
      },
      {
        title: "Courses",
        link: "/element-courses",
        pluseIncon: false,
      },
      {
        title: "Call To Action",
        link: "/element-cta",
        pluseIncon: false,
      },
      {
        title: "Newsletter",
        link: "/element-newsletter",
        pluseIncon: false,
      },
      {
        title: "Progress Bar",
        link: "/element-progress-bar",
        pluseIncon: false,
      }
    ]
  },
  {
    id: 6,
    hasDropdown: true,
    children: true,
    megaMenu: false,
    active: true,
    title: "Blog",
    pluseIncon: true,
    lastDropdown: true,
    link: "#",
    submenus: [
      {
        title: "Blog Standard",
        link: "/blog",
        pluseIncon: false,
      },
      {
        title: "Blog Advanced",
        link: "#",
        pluseIncon: true,
        megaMenu: [
          { title: "Blog List", link: "/blog-list" },
          { title: "Blog Grid", link: "/blog-grid" }
        ],
      },
      {
        title: "blog-details",
        link: "/blog/blog-details",
        pluseIncon: false,
      }
    ],
  }
];

export default university_menu_data;
