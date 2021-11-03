import React from "react";
import { BrowserRouter, Route } from "react-router-dom";
import ViewProduct from "./ViewProduct";

export default function Root(props) {
    return (
        <BrowserRouter>
            <Route path="/product/:id" component={ViewProduct} />
        </BrowserRouter>
    );
}
