// resources/js/landbot/flowConfig.js
import withHandles from "../landbot/components/withHandles";
import {
  StartingNode,
  QuestionNode,
  ButtonsNode,
  YesNoNode,
  RatingNode,
  MessageNode,
  ConditionNode,
  FormulaNode,
  LogicNode,
} from "../landbot/components/index";

export const nodeTypes = {
  starting: StartingNode, // keep starting unwrapped

  // wrap others with both left target + right invisible source
  question: withHandles(QuestionNode),
  buttons: withHandles(ButtonsNode),
  yesno: withHandles(YesNoNode),
  rating: withHandles(RatingNode),
  message: withHandles(MessageNode),
  condition: withHandles(ConditionNode),
  formula: withHandles(FormulaNode),
  logic: withHandles(LogicNode),
};

export const initialNodes = [
  {
    id: "1",
    type: "starting",
    position: { x: 100, y: 400 },
    data: {},
    draggable: false,
    selectable: false,
  },
];
