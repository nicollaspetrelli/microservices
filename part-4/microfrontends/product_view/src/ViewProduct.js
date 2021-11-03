import React from "react";
import Col from "react-bootstrap/Col";
import Container from "react-bootstrap/Container";
import Row from "react-bootstrap/Row";

export default function Root(props, b = null) {
    const {id} = props.match.params;

    return (
        <Container>
            <h1>Produto {id}</h1>
            <Row>
                <Col md={6}>
                    <img src="https://via.placeholder.com/1024" style={{maxWidth: '100%'}}/>
                </Col>
                <Col md={6}>
                    <p>
                        Lorem ipsum dolor sit amet, consectetur adipiscing elit. Phasellus mollis feugiat nisi, nec
                        rhoncus diam. Morbi vulputate tincidunt massa, et porttitor ligula tristique nec. In gravida
                        rhoncus malesuada. Nam a enim nulla. Fusce malesuada libero eu volutpat tincidunt. Nulla ut
                        mollis lorem. Nam faucibus purus eget risus hendrerit feugiat. Integer laoreet dignissim elit
                        quis auctor. Nulla facilisi. Nulla ut arcu malesuada, rutrum elit non, finibus nisl. Praesent
                        erat odio, vestibulum sed ante sed, convallis faucibus felis.
                    </p>
                    <p>

                        Suspendisse congue commodo diam sit amet egestas. Etiam a enim tellus. Integer gravida
                        vestibulum mi, sed scelerisque lacus. Lorem ipsum dolor sit amet, consectetur adipiscing elit.
                        Pellentesque quis purus eu dui lobortis aliquam. Duis mauris justo, pharetra quis dolor eget,
                        rutrum interdum nisi. Nulla facilisi. Donec condimentum aliquam mi eget laoreet. Praesent eu
                        posuere enim. Nulla dignissim venenatis dui ac vulputate. Phasellus faucibus tincidunt orci,
                        vitae pretium neque ornare nec. Morbi justo nibh, tristique a erat quis, ullamcorper accumsan
                        orci. Fusce ut lacus rutrum, consectetur ante at, convallis purus.
                    </p>
                </Col>
            </Row>
        </Container>
    );
};
