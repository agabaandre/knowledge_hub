
import { MenuItem } from "@/interFace/interFace";
import homeOneImg from "../../../public/assets/images/menu/menu-home-1.webp";
import homeTowImg from "../../../public/assets/images/menu/menu-home-2.webp";
import homeThreeImg from "../../../public/assets/images/menu/menu-home-3.webp";
import homeFourImg from "../../../public/assets/images/menu/menu-home-4.webp";
import homeFiveImg from "../../../public/assets/images/menu/menu-home-5.webp";
import homeSixImg from "../../../public/assets/images/menu/menu-home-6.webp";
import homeSevenImg from "../../../public/assets/images/menu/menu-home-7.webp";
import coummingSoonImg from "../../../public/assets/images/menu/menu-home-soon.webp";

const main_mobile_menu_data: MenuItem[] = [
  {
    id: 1,
    hasDropdown: true,
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
    title: "Courses",
    pluseIncon: true,
    link: "#",
    submenus: [
      {
        title: "Advanced Course Filter",
        link: "#",
        pluseIncon: true,
        megaMenu: [
          { title: "Courses", link: "/courses" },
          { title: "Courses Search Filter", link: "/courses-filter-search" },
          { title: "Courses Filter Category", link: "/courses-filter-category" }
        ],
      },
      {
        title: "Courses Grid",
        link: "#",
        pluseIncon: true,
        megaMenu: [
          { title: "Courses Grid", link: "/courses-grid" },
          { title: "Courses Grid Two", link: "/courses-grid-two" },
          { title: "Courses Grid Three", link: "/courses-grid-three" },
          { title: "Courses Grid Four", link: "/courses-grid-four" },
          { title: "Courses Grid Five", link: "/courses-grid-five" },
          { title: "Courses Grid Right Sidebar", link: "/courses-grid-right" },
          { title: "Courses Grid Left Sidebar", link: "/courses-grid-left" },
        ],
      },
      {
        title: "Courses List",
        link: "#",
        pluseIncon: true,
        megaMenu: [
          { title: "Courses Left Sidebar", link: "/courses-list-one" },
          { title: "Courses Right Sidebar", link: "/courses-list-two" },
          { title: "No Sidebar", link: "/courses-list-three" },
        ],
      },
      {
        title: "Courses Details",
        link: "#",
        pluseIncon: true,
        megaMenu: [
          { title: "Courses Details", link: "/courses/course-details" },
          { title: "Courses Details Two", link: "/courses/course-details-two" },
          { title: "Courses Details Three", link: "/courses/course-details-three" },
        ],
      },
      {
        title: "Program Details",
        link: "#",
        pluseIncon: true,
        megaMenu: [
          { title: "University Program", link: "/program-details" },
          { title: "kindergarten Program", link: "/kindergarten-program-details" },
        ],
      },
      {
        title: "Courses Lesson",
        link: "/course-lesson",
        pluseIncon: false,
      },
      {
        title: "Create New Course",
        link: "/create-course",
        pluseIncon: false,
      },
    ],
  },
  {
    id: 3,
    hasDropdown: true,
    children: false,
    megaMenu: true,
    active: true,
    title: "Pages",
    pluseIncon: true,
    pageLayout: true,
    link: "#",
    submenus: [
      {
        title: "Page Layout 1",
        link: "#",
        megaMenu: [
          { title: "Contact Us", link: "/contact-us" },
          { title: "About Online Course", link: "/about-online-course" },
          { title: "About University", link: "/about-university" },
          { title: "About Modern Schooling", link: "/about-modern-schooling" },
          { title: "About Kindergarten", link: "/about-kindergarten" },
          { title: "About Quarn Learning", link: "/about-quran-learning" },
          { title: "Vision, Mission & Strategy", link: "/mvs" },
          { title: "History", link: "/history" },
        ]
      },
      {
        title: "Page Layout 2",
        link: "#",
        megaMenu: [
          { title: "Campus", link: "/campus" },
          { title: "Academic Calendar", link: "/academic-calendar" },
          { title: "Apply Online", link: "/apply-online" },
          { title: "Executive Leaders", link: "/executive-leaders" },
          { title: "Faculty Members", link: "/faculty-members" },
          { title: "Tuition and Other Fees", link: "/tuition-fees" },
          { title: "Scholarships & Financial Aid", link: "/scholarships-financial-aid" },
          { title: "Scholarships Details", link: "/scholarships-financial-aid-details" }
        ]
      },
      {
        title: "Page Layout 3",
        link: "#",
        megaMenu: [
          { title: "Instructor", link: "/instructor" },
          { title: "Instructor Details", link: "/instructor/instructor-details" },
          { title: "Become Instructor", link: "/become-instructor" },
          { title: "Event Grid", link: "/event" },
          { title: "Event List", link: "/event-list" },
          { title: "Event Details", link: "/event/event-details" },
          { title: "Webinar Details", link: "/webinar-details" },
          { title: "Faq's", link: "/faq" }
        ]
      },
      {
        title: "Page Layout 4",
        link: "#",
        megaMenu: [
          { title: "Shop", link: "/shop" },
          { title: "Shop V2", link: "/shop-v2" },
          { title: "Book Details", link: "/shop/shop-details" },
          { title: "Shop Details", link: "/shop/shop-details-two" },
          { title: "Checkout", link: "/checkout" },
          { title: "Cart", link: "/cart" },
          { title: "Wishlist", link: "/wishlist" },
          { title: "Pricing Table", link: "/pricing-table" }
        ]
      },
      {
        title: "Page Layout 5",
        link: "#",
        megaMenu: [
          { title: "Sign In", link: "/sign-in" },
          { title: "Sign Up", link: "/sign-up" },
          { title: "Forgot Password", link: "/forgot" },
          { title: "404 Page", link: "/error-page" },
          { title: "Terms And Conditions", link: "/terms-conditions" },
          { title: "Privacy & Policy", link: "/privacy-policy" },
          { title: "Coming Soon", link: "/coming-soon" },
          { title: "Under Maintenance", link: "/under-maintenance" }
        ]
      }
    ]
  },
  {
    id: 4,
    hasDropdown: true,
    active: true,
    megaMenu: false,
    children: true,
    title: "Dashboard",
    pluseIncon: true,
    link: "#",
    submenus: [
      {
        title: "Instructor Dashboard",
        link: "#",
        pluseIncon: true,
        megaMenu: [
          { title: "Instructor Dashboard", link: "/instructor-dashboard" },
          { title: "Instructor Profile", link: "/instructor-profile" },
          { title: "Enrolled Courses", link: "/instructor-enrolled-courses" },
          { title: "Instructor Quiz Attempts", link: "/instructor-my-quiz-attempts" },
          { title: "Instructor Assignments", link: "/instructor-assignments" },
          { title: "Instructor Analytics", link: "/instructor-analytics" },
          { title: "My Courses", link: "/instructor-courses" },
          { title: "Instructor Books", link: "/instructor-books" },
          { title: "Instructor Wishlist", link: "/instructor-wishlist" },
          { title: "Instructor Reviews", link: "/instructor-reviews" },
          { title: "Instructor Purchase History", link: "/instructor-purchase-history" },
          { title: "Instructor Announcements", link: "/instructor-announcements" },
          { title: "Instructor Certificate", link: "/instructor-certificate" },
          { title: "Instructor Settings", link: "/instructor-settings" },
        ],
      },
      {
        title: "Student Dashboard",
        link: "#",
        pluseIncon: true,
        megaMenu: [
          { title: "Student Dashboard", link: "/student-dashboard" },
          { title: "Student Profile", link: "/student-profile" },
          { title: "Enrolled Courses", link: "/student-enrolled-courses" },
          { title: "Student Quiz Attempts", link: "/student-my-quiz-attempts" },
          { title: "Student Assignments", link: "/student-assignments" },
          { title: "Student Analytics", link: "/student-analytics" },
          { title: "Student Books", link: "/student-books" },
          { title: "Student Wishlist", link: "/student-wishlist" },
          { title: "Student Reviews", link: "/student-reviews" },
          { title: "Student Purchase History", link: "/student-purchase-history" },
          { title: "Student Announcements", link: "/student-announcements" },
          { title: "My Achievement", link: "/student-certificate" },
          { title: "Student Settings", link: "/student-settings" },
        ],
      }
    ],
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
          { title: "Blog Grid", link: "/blog-grid" },
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

export default main_mobile_menu_data;
