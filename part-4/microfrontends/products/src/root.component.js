import React from "react";
import Button from "react-bootstrap/Button";
import Card from "react-bootstrap/Card";
import Container from "react-bootstrap/Container";
import Col from "react-bootstrap/Col";
import Row from "react-bootstrap/Row";

export default function Root() {
    const items = [];
    for (let i = 1; i < 6; ++i) {
        items.push(
            <Col key={i} md={6} lg={4}>
                <Card className="my-2">
                    <Card.Img variant="top" src="https://via.placeholder.com/500"/>
                    <Card.Body>
                        <Card.Title>Produto {i}</Card.Title>
                        <Card.Text>
                            Lorem ipsum dolor sit amet, consectetur adipiscing elit. Suspendisse fringilla enim urna,
                            vitae egestas nisi condimentum tristique. Nullam ut ornare dui. Vestibulum convallis
                            convallis tellus id consectetur.
                        </Card.Text>
                        <div className="d-flex justify-content-between align-items-center">
                            <p className="text-success">
                                R$ <span className="fs-5">60,00</span>
                            </p>
                            <Button variant="primary" href={"/product/" + i}>Ver detalhes</Button>
                        </div>
                    </Card.Body>
                </Card>
            </Col>
        );
    }

    return (
        <Container>
            <h1>Produtos</h1>
            <Row>
                {items}
            </Row>
        </Container>
    );
}
