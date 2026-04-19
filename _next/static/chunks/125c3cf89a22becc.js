(globalThis.TURBOPACK || (globalThis.TURBOPACK = [])).push(["object" == typeof document ? document.currentScript : void 0, 18566, (e, t, s) => {
    t.exports = e.r(76562)
}, 50167, e => {
    "use strict";
    let t = {
        telegramBotToken: "",
        telegramChatId: "",
        storeName: "مزارع الثنيان",
        currencyLabel: "د.ك",
        currencyCode: "KD",
        siteLogo: "/logo.png"
    };

    function s(e, t = "") {
        return "string" == typeof e ? e : t
    }

    function i(e) {
        let i = "object" == typeof e && null !== e ? e : {};
        return {
            telegramBotToken: s(i.telegramBotToken, t.telegramBotToken),
            telegramChatId: s(i.telegramChatId, t.telegramChatId),
            storeName: s(i.storeName, t.storeName),
            currencyLabel: s(i.currencyLabel, t.currencyLabel),
            currencyCode: s(i.currencyCode, t.currencyCode),
            siteLogo: s(i.siteLogo, t.siteLogo)
        }
    }

    function n(e, t) {
        return `${e.toFixed(3)} ${t}`
    }
    e.s(["defaultSiteSettings", 0, t, "formatMoney", () => n, "normalizeSettings", () => i])
}, 47683, e => {
    "use strict";
    var t = e.i(71645),
        s = e.i(50167);

    function i() {
        let [e, i] = (0, t.useState)(s.defaultSiteSettings);
        return (0, t.useEffect)(() => {
            let e = !0;
            return fetch("/api/settings.json").then(e => e.json()).then(t => {
                e && i((0, s.normalizeSettings)(t))
            }).catch(() => {
                e && i(s.defaultSiteSettings)
            }), () => {
                e = !1
            }
        }, []), e
    }
    e.s(["useSiteSettings", () => i])
}, 61618, e => {
    "use strict";
    var t = e.i(43476),
        s = e.i(25194),
        i = e.i(18566),
        n = e.i(71645),
        l = e.i(50167),
        x = e.i(47683);

    function r({
        product: e
    }) {
        let {
            items: i,
            addItem: n,
            removeItem: l
        } = (0, s.useCart)(), x = i.find(t => t.product.id === e.id), r = x?.quantity || 0;
        return 0 === r ? (0, t.jsx)("button", {
            onClick: () => n(e),
            className: "rounded-[20px] bg-[#f5f5f5] px-[24px] py-[12px] text-[14px] font-semibold text-[#000] hover:bg-[#e8e8e8] transition",
            children: "إضافة"
        }) : (0, t.jsxs)("div", {
            className: "flex items-center gap-3 bg-[#f5f5f5] rounded-[28px] px-3 py-1.5",
            children: [(0, t.jsx)("button", {
                onClick: () => l(e.id),
                className: "w-[28px] h-[28px] flex items-center justify-center text-lg",
                children: "−"
            }), (0, t.jsx)("span", {
                className: "font-extrabold text-[16px] min-w-[20px] text-center",
                children: r
            }), (0, t.jsx)("button", {
                onClick: () => n(e),
                className: "w-[28px] h-[28px] flex items-center justify-center text-lg",
                children: "+"
            })]
        })
    }

    function c() {
        let {
            items: W,
            getTotal: e,
            getCount: c
        } = (0, s.useCart)(), a = (0, i.useRouter)(), d = (0, x.useSiteSettings)(), o = e(), p = c(), [h, m] = (0, n.useState)([]);
        return (0, n.useEffect)(() => {
            fetch("/api/products.json").then(e => e.json()).then(m).catch(() => {})
        }, []), (0, t.jsxs)("div", {
            className: "bg-white min-h-screen pb-[80px]",
            children: [(0, t.jsx)("header", {
                className: "sticky top-0 z-50 bg-white",
                style: {
                    boxShadow: "0 4px 10px rgba(0,0,0,0.08)"
                },
                children: (0, t.jsxs)("div", {
                    className: "flex items-center justify-between px-3 py-2.5",
                    children: [(0, t.jsx)("button", {
                        className: "bg-[#f5f5f5] rounded-full w-[40px] h-[40px] flex items-center justify-center",
                        children: (0, t.jsxs)("svg", {
                            width: "18",
                            height: "18",
                            viewBox: "0 0 24 24",
                            fill: "none",
                            stroke: "currentColor",
                            strokeWidth: "2",
                            children: [(0, t.jsx)("line", {
                                x1: "3",
                                y1: "6",
                                x2: "21",
                                y2: "6"
                            }), (0, t.jsx)("line", {
                                x1: "3",
                                y1: "12",
                                x2: "21",
                                y2: "12"
                            }), (0, t.jsx)("line", {
                                x1: "3",
                                y1: "18",
                                x2: "21",
                                y2: "18"
                            })]
                        })
                    }), (0, t.jsx)("div", {
                        className: "flex items-center gap-2",
                        children: (0, t.jsx)("img", {
                            src: d.siteLogo,
                            alt: d.storeName,
                            className: "h-[48px] w-auto object-contain"
                        })
                    }), (0, t.jsxs)("button", {
                        onClick: () => {
                            if (p > 0) {
                                try {
                                    let s = JSON.stringify(W);
                                    localStorage.setItem("farm_cart_v1", s);
                                    sessionStorage.setItem("farm_cart_v1", s)
                                } catch (z) {}
                                location.href = "/cart.html"
                            }
                        },
                        className: "bg-[#025380] text-white rounded-[28px] px-4 py-2 flex items-center gap-2 text-[14px] font-semibold",
                        children: [(0, t.jsxs)("svg", {
                            width: "20",
                            height: "20",
                            viewBox: "0 0 24 24",
                            fill: "none",
                            stroke: "currentColor",
                            strokeWidth: "2",
                            children: [(0, t.jsx)("path", {
                                d: "M6 2L3 6v14a2 2 0 002 2h14a2 2 0 002-2V6l-3-4z"
                            }), (0, t.jsx)("line", {
                                x1: "3",
                                y1: "6",
                                x2: "21",
                                y2: "6"
                            }), (0, t.jsx)("path", {
                                d: "M16 10a4 4 0 01-8 0"
                            })]
                        }), (0, t.jsx)("span", {
                            children: (0, l.formatMoney)(o, d.currencyLabel)
                        })]
                    })]
                })
            }), (0, t.jsx)("div", {
                className: "py-5 px-3",
                children: (0, t.jsx)("h1", {
                    className: "text-[36px] font-black text-center leading-[40px]",
                    children: d.storeName
                })
            }), (0, t.jsxs)("div", {
                className: "flex items-center justify-center gap-2 px-3 pb-4 flex-wrap",
                children: [(0, t.jsxs)("div", {
                    className: "bg-[#f5f5f5] rounded-[28px] px-4 py-2 flex items-center gap-1.5 text-[14px] font-medium",
                    children: [(0, t.jsxs)("svg", {
                        width: "20",
                        height: "20",
                        viewBox: "0 0 24 24",
                        fill: "#017bff",
                        stroke: "none",
                        children: [(0, t.jsx)("circle", {
                            cx: "12",
                            cy: "12",
                            r: "10"
                        }), (0, t.jsx)("path", {
                            d: "M8 12l2 2 4-4",
                            stroke: "white",
                            strokeWidth: "2",
                            fill: "none"
                        })]
                    }), (0, t.jsx)("span", {
                        children: "توصيل مجاني"
                    })]
                }), (0, t.jsx)("div", {
                    className: "bg-[#f5f5f5] rounded-[28px] px-4 py-2 text-[14px] font-medium",
                    children: "خلال 40 دقيقة"
                }), (0, t.jsx)("div", {
                    className: "bg-[#f5f5f5] rounded-[28px] px-4 py-2 text-[14px] font-medium",
                    children: "نقل مخصص"
                })]
            }), (0, t.jsxs)("div", {
                className: "flex items-center justify-around px-3 py-4 border-t border-b border-[#f5f5f5]",
                children: [(0, t.jsxs)("div", {
                    className: "text-center",
                    children: [(0, t.jsx)("p", {
                        className: "text-[14px] font-bold",
                        children: "40 دقيقة"
                    }), (0, t.jsx)("p", {
                        className: "text-[12px] font-medium text-[#707070]",
                        children: "وقت التوصيل"
                    })]
                }), (0, t.jsx)("div", {
                    className: "w-px h-8 bg-[#e0e0e0]"
                }), (0, t.jsxs)("div", {
                    className: "text-center",
                    children: [(0, t.jsx)("p", {
                        className: "text-[14px] font-bold",
                        children: "4.7"
                    }), (0, t.jsx)("p", {
                        className: "text-[12px] font-medium text-[#707070]",
                        children: "التقييم"
                    })]
                }), (0, t.jsx)("div", {
                    className: "w-px h-8 bg-[#e0e0e0]"
                }), (0, t.jsxs)("div", {
                    className: "text-center",
                    children: [(0, t.jsx)("p", {
                        className: "text-[14px] font-bold",
                        children: "15 كيلو"
                    }), (0, t.jsx)("p", {
                        className: "text-[12px] font-medium text-[#707070]",
                        children: "المسافة"
                    })]
                }), (0, t.jsx)("div", {
                    className: "w-px h-8 bg-[#e0e0e0]"
                }), (0, t.jsxs)("div", {
                    className: "text-center",
                    children: [(0, t.jsx)("p", {
                        className: "text-[14px] font-bold text-[#22c55e]",
                        children: "مفتوح"
                    }), (0, t.jsx)("p", {
                        className: "text-[12px] font-medium text-[#707070]",
                        children: "ساعات الـ"
                    })]
                })]
            }), (0, t.jsx)("div", {
                className: "px-3 pt-4",
                children: h.map(e => (0, t.jsxs)("div", {
                    className: "flex gap-3 py-4 border-b border-[#f5f5f5]",
                    children: [(0, t.jsx)("div", {
                        className: "w-[120px] h-[120px] rounded-[16px] overflow-hidden flex-shrink-0 relative bg-[#f5f5f5]",
                        children: (0, t.jsx)("img", {
                            src: e.image,
                            alt: e.name,
                            className: "absolute inset-0 w-full h-full object-cover"
                        })
                    }), (0, t.jsxs)("div", {
                        className: "flex-1 flex flex-col justify-between min-h-[120px]",
                        children: [(0, t.jsxs)("div", {
                            children: [(0, t.jsx)("h5", {
                                className: "text-[16px] font-extrabold leading-[20px] mb-1",
                                children: e.name
                            }), (0, t.jsx)("p", {
                                className: "text-[12px] font-medium text-[#707070] leading-[16px]",
                                children: e.description
                            })]
                        }), (0, t.jsxs)("div", {
                            className: "flex items-center justify-between mt-2",
                            children: [(0, t.jsx)("h5", {
                                className: "text-[16px] font-extrabold",
                                children: (0, l.formatMoney)(e.price, d.currencyLabel)
                            }), (0, t.jsx)(r, {
                                product: e
                            })]
                        })]
                    })]
                }, e.id))
            }), (0, t.jsx)("footer", {
                className: "px-3 py-5 mt-4",
                children: (0, t.jsxs)("div", {
                    className: "flex items-center justify-between text-[12px]",
                    children: [(0, t.jsxs)("div", {
                        className: "flex gap-3",
                        children: [(0, t.jsx)("a", {
                            href: "#",
                            className: "text-[#707070] hover:underline",
                            children: "الشروط والأحكام"
                        }), (0, t.jsx)("a", {
                            href: "#",
                            className: "text-[#707070] hover:underline",
                            children: "سياسة الخصوصية"
                        })]
                    }), (0, t.jsxs)("p", {
                        className: "text-[#707070]",
                        children: ["© 2024 ", d.storeName]
                    })]
                })
            }), p > 0 && (0, t.jsx)("div", {
                className: "fixed bottom-0 left-1/2 -translate-x-1/2 w-full max-w-[480px] z-50",
                onClick: () => {
                    try {
                        let s = JSON.stringify(W);
                        localStorage.setItem("farm_cart_v1", s);
                        sessionStorage.setItem("farm_cart_v1", s)
                    } catch (z) {}
                    location.href = "/cart.html"
                },
                children: (0, t.jsxs)("div", {
                    className: "bg-[#025380] mx-3 mb-3 rounded-[28px] px-4 py-3 flex items-center justify-between cursor-pointer",
                    children: [(0, t.jsxs)("div", {
                        className: "flex items-center gap-2",
                        children: [(0, t.jsx)("span", {
                            className: "bg-white text-[#025380] w-[28px] h-[28px] rounded-full flex items-center justify-center text-[14px] font-bold",
                            children: p
                        }), (0, t.jsx)("h5", {
                            className: "text-white text-[16px] font-extrabold",
                            children: (0, l.formatMoney)(o, d.currencyLabel)
                        })]
                    }), (0, t.jsxs)("div", {
                        className: "flex items-center gap-2",
                        children: [(0, t.jsx)("h5", {
                            className: "text-white text-[16px] font-extrabold",
                            children: "اذهب الى السلة"
                        }), (0, t.jsx)("svg", {
                            width: "20",
                            height: "20",
                            viewBox: "0 0 24 24",
                            fill: "none",
                            stroke: "white",
                            strokeWidth: "2",
                            children: (0, t.jsx)("polyline", {
                                points: "15 18 9 12 15 6"
                            })
                        })]
                    })]
                })
            })]
        })
    }
    e.s(["default", () => c])
}]);