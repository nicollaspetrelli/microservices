import React from "react";
import Container from "react-bootstrap/Container";
import Button from "react-bootstrap/Button";

export default function Root() {
    return (
        <Container>
            <h1>Microfrontends</h1>
            <p>Essa é a página inicial do nosso <em>container</em>. Acesse as páginas através da navegação abaixo.</p>
            <Button variant="success" href="/products" size="lg">Ver produtos</Button>
        </Container>
    );
}
