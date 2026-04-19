(globalThis.TURBOPACK || (globalThis.TURBOPACK = [])).push(["object" == typeof document ? document.currentScript : void 0, 25194, t => {
    "use strict";
    var e = t.i(43476),
        r = t.i(71645);
    let i = (0, r.createContext)(void 0);

    function u({
        children: t
    }) {
        let[u,d]=(0,r.useState)(()=>{try{let e=localStorage.getItem("farm_cart_v1")||sessionStorage.getItem("farm_cart_v1");return e?JSON.parse(e):[]}catch(e){return[]}});(0,r.useEffect)(()=>{try{let s=JSON.stringify(u);localStorage.setItem("farm_cart_v1",s);sessionStorage.setItem("farm_cart_v1",s)}catch(e){}},[u]);return(0,e.jsx)(i.Provider,{
            value: {
                items: u,
                addItem: t => {
                    d(e => {
                        let n = e.find(e => e.product.id === t.id) ? e.map(e => e.product.id === t.id ? {
                            ...e,
                            quantity: e.quantity + 1
                        } : e) : [...e, {
                            product: t,
                            quantity: 1
                        }];
                        try {
                            let s = JSON.stringify(n);
                            localStorage.setItem("farm_cart_v1", s);
                            sessionStorage.setItem("farm_cart_v1", s)
                        } catch (x) {}
                        return n
                    })
                },
                removeItem: t => {
                    d(e => {
                        let r = e.find(e => e.product.id === t),
                            n = r && r.quantity > 1 ? e.map(e => e.product.id === t ? {
                                ...e,
                                quantity: e.quantity - 1
                            } : e) : e.filter(e => e.product.id !== t);
                        try {
                            let s = JSON.stringify(n);
                            localStorage.setItem("farm_cart_v1", s);
                            sessionStorage.setItem("farm_cart_v1", s)
                        } catch (x) {}
                        return n
                    })
                },
                getTotal: () => u.reduce((t, e) => t + e.product.price * e.quantity, 0),
                getCount: () => u.reduce((t, e) => t + e.quantity, 0),
                clearCart: () => d(() => {
                    try {
                        localStorage.setItem("farm_cart_v1", "[]");
                        sessionStorage.setItem("farm_cart_v1", "[]")
                    } catch (x) {}
                    return []
                })
            },
            children: t
        })
    }

    function d() {
        let t = (0, r.useContext)(i);
        if (!t) throw Error("useCart must be used within CartProvider");
        return t
    }
    t.s(["CartProvider", () => u, "useCart", () => d])
}, 45374, t => {
    "use strict";
    var e = t.i(43476),
        r = t.i(25194);

    function i({
        children: t
    }) {
        return (0, e.jsx)("div", {
            className: "max-w-[480px] mx-auto bg-white min-h-screen relative",
            children: (0, e.jsx)(r.CartProvider, {
                children: t
            })
        })
    }
    t.s(["default", () => i])
}]);