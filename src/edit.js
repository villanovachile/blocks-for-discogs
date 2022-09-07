import { useBlockProps } from '@wordpress/block-editor';
import './editor.scss';
import ServerSideRender from '@wordpress/server-side-render';

export default function Edit() {
	return (
		<div {...useBlockProps()}>
			<ServerSideRender block="drdb/discogs-block" />
		</div>
	);
}
